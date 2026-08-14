<?php

namespace App\Http\Controllers;

use App\Mail\MarketplaceNotificationMail;
use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\Conversation;
use App\Models\JobPosting;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContractController extends Controller
{
    public function createFromProposal(Proposal $proposal)
    {
        $job = $proposal->jobPosting;

        if (Auth::id() !== $job->client_id) {
            abort(403, 'Only the job client can hire a freelancer.');
        }

        $proposal->load('freelancer.freelancerProfile');

        return view('contracts.hire', compact('proposal', 'job'));
    }

    public function storeHire(Request $request, Proposal $proposal)
    {
        $job = $proposal->jobPosting;

        if (Auth::id() !== $job->client_id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:5',
            'terms' => 'nullable|string',
            'milestone_titles' => 'nullable|array',
            'milestone_amounts' => 'nullable|array',
            'fund_first_milestone' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($proposal, $job, $validated, $request) {
            $contract = Contract::create([
                'job_posting_id' => $job->id,
                'proposal_id' => $proposal->id,
                'client_id' => Auth::id(),
                'freelancer_id' => $proposal->freelancer_id,
                'title' => $validated['title'],
                'type' => $job->type,
                'amount' => (float) $validated['amount'],
                'platform_fee_percent' => 10.00,
                'status' => 'active',
                'terms' => $validated['terms'] ?? 'Standard freelance agreement',
                'start_date' => now(),
            ]);

            // Create milestones
            if (!empty($validated['milestone_titles'])) {
                foreach ($validated['milestone_titles'] as $idx => $mTitle) {
                    if (!empty($mTitle)) {
                        $mAmt = (float) ($validated['milestone_amounts'][$idx] ?? 0);
                        ContractMilestone::create([
                            'contract_id' => $contract->id,
                            'title' => $mTitle,
                            'amount' => $mAmt,
                            'status' => 'pending',
                        ]);
                    }
                }
            } else {
                ContractMilestone::create([
                    'contract_id' => $contract->id,
                    'title' => 'Complete Project Delivery',
                    'amount' => (float) $validated['amount'],
                    'status' => 'pending',
                ]);
            }

            // Update proposal & job
            $proposal->update(['status' => 'accepted']);
            $job->update(['status' => 'in_progress']);
            $job->increment('hires_count');

            // Create or associate chat conversation
            $conv = Conversation::firstOrCreate([
                'job_posting_id' => $job->id,
                'contract_id' => $contract->id,
            ], [
                'subject' => $contract->title,
                'last_message_at' => now(),
            ]);
            $conv->participants()->syncWithoutDetaching([$contract->client_id, $contract->freelancer_id]);

            // Auto fund first milestone if requested
            if ($request->boolean('fund_first_milestone')) {
                $firstM = $contract->milestones()->first();
                if ($firstM) {
                    $this->fundMilestoneInternal($firstM, Auth::user());
                }
            }

            // Send You Are Hired Email to Freelancer
            try {
                $freelancer = $proposal->freelancer;
                if ($freelancer && $freelancer->email) {
                    Mail::to($freelancer->email)->send(
                        new MarketplaceNotificationMail(
                            subject: '🎉 Congratulations! You have been hired for "' . $contract->title . '"',
                            greeting: 'Hi ' . $freelancer->name . ',',
                            mainMessage: Auth::user()->name . ' has hired you on WorkForge for project "' . $contract->title . '".',
                            actionUrl: route('contracts.show', $contract->id),
                            actionText: 'Open Contract Workroom',
                            details: [
                                'Client' => Auth::user()->name,
                                'Contract Value' => '$' . number_format($contract->amount, 2),
                                'Status' => 'Active Contract',
                            ]
                        )
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Hired email failed: ' . $e->getMessage());
            }

            return redirect()->route('contracts.show', $contract->id)->with('success', 'Contract successfully created and freelancer hired!');
        });
    }

    public function show(Contract $contract)
    {
        $user = Auth::user();
        if ($user->id !== $contract->client_id && $user->id !== $contract->freelancer_id && !$user->isAdmin()) {
            abort(403);
        }

        $contract->load([
            'client.clientProfile',
            'freelancer.freelancerProfile',
            'milestones',
            'reviews',
            'jobPosting',
        ]);

        return view('contracts.show', compact('contract', 'user'));
    }

    public function fundMilestone(Request $request, ContractMilestone $milestone)
    {
        $contract = $milestone->contract;
        if (Auth::id() !== $contract->client_id) {
            abort(403);
        }

        $result = $this->fundMilestoneInternal($milestone, Auth::user());

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Milestone funded into Escrow successfully ($' . number_format($milestone->amount, 2) . ').');
    }

    private function fundMilestoneInternal(ContractMilestone $milestone, User $client): array
    {
        $wallet = Wallet::firstOrCreate(['user_id' => $client->id]);

        if ($wallet->balance < $milestone->amount) {
            $needed = $milestone->amount - $wallet->balance + 1000;
            $wallet->balance += $needed;
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $client->id,
                'type' => 'deposit',
                'amount' => $needed,
                'fee' => 0,
                'description' => 'Demo wallet auto-topup for escrow funding',
                'status' => 'completed',
            ]);
        }

        $wallet->balance -= $milestone->amount;
        $wallet->escrow_balance += $milestone->amount;
        $wallet->save();

        $milestone->update([
            'status' => 'funded_in_escrow',
            'funded_at' => now(),
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $client->id,
            'type' => 'escrow_lock',
            'amount' => $milestone->amount,
            'fee' => 0,
            'reference_type' => 'ContractMilestone',
            'reference_id' => $milestone->id,
            'description' => "Escrow funded for Milestone: {$milestone->title}",
            'status' => 'completed',
        ]);

        // Send Email to Freelancer
        try {
            $freelancer = $milestone->contract->freelancer;
            if ($freelancer && $freelancer->email) {
                Mail::to($freelancer->email)->send(
                    new MarketplaceNotificationMail(
                        subject: '🔒 Milestone Funded in Escrow ($' . number_format($milestone->amount, 2) . ')',
                        greeting: 'Hi ' . $freelancer->name . ',',
                        mainMessage: 'Great news! ' . $client->name . ' has funded Milestone: "' . $milestone->title . '" into Escrow protection. You can now begin work safely.',
                        actionUrl: route('contracts.show', $milestone->contract_id),
                        actionText: 'View Milestone & Start Work',
                        details: [
                            'Contract' => $milestone->contract->title,
                            'Funded Amount' => '$' . number_format($milestone->amount, 2),
                            'Escrow Status' => 'Protected by WorkForge',
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Funded email failed: ' . $e->getMessage());
        }

        return ['success' => true];
    }

    public function submitWork(Request $request, ContractMilestone $milestone)
    {
        $contract = $milestone->contract;
        if (Auth::id() !== $contract->freelancer_id) {
            abort(403);
        }

        $validated = $request->validate([
            'submission_notes' => 'required|string|min:10',
        ]);

        $milestone->update([
            'status' => 'submitted_for_approval',
            'submission_notes' => $validated['submission_notes'],
            'submitted_at' => now(),
        ]);

        // Send Deliverables Notification to Client
        try {
            $client = $contract->client;
            if ($client && $client->email) {
                Mail::to($client->email)->send(
                    new MarketplaceNotificationMail(
                        subject: '📦 Work Submitted for Review: "' . $milestone->title . '"',
                        greeting: 'Hi ' . $client->name . ',',
                        mainMessage: Auth::user()->name . ' has submitted deliverable work for Milestone: "' . $milestone->title . '". Please review the notes and approve payment.',
                        actionUrl: route('contracts.show', $contract->id),
                        actionText: 'Review Work & Release Payment',
                        details: [
                            'Contract' => $contract->title,
                            'Milestone' => $milestone->title,
                            'Amount' => '$' . number_format($milestone->amount, 2),
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Submit work email failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Work submitted for client review and approval!');
    }

    public function releasePayment(ContractMilestone $milestone)
    {
        $contract = $milestone->contract;
        if (Auth::id() !== $contract->client_id) {
            abort(403);
        }

        return DB::transaction(function () use ($milestone, $contract) {
            $clientWallet = Wallet::firstOrCreate(['user_id' => $contract->client_id]);
            $freelancerWallet = Wallet::firstOrCreate(['user_id' => $contract->freelancer_id]);

            // Platform fee calculation (10%)
            $platformFee = round($milestone->amount * ($contract->platform_fee_percent / 100), 2);
            $freelancerReceives = $milestone->amount - $platformFee;

            // Escrow balance deduction
            if ($clientWallet->escrow_balance >= $milestone->amount) {
                $clientWallet->escrow_balance -= $milestone->amount;
            } else {
                $clientWallet->balance -= $milestone->amount;
            }
            $clientWallet->save();

            // Freelancer wallet credit
            $freelancerWallet->balance += $freelancerReceives;
            $freelancerWallet->save();

            // Update stats
            $contract->freelancer->freelancerProfile?->increment('total_earnings', $freelancerReceives);
            $contract->client->clientProfile?->increment('total_spent', $milestone->amount);

            $milestone->update([
                'status' => 'approved_and_released',
                'released_at' => now(),
            ]);

            // Log ledger transactions
            Transaction::create([
                'wallet_id' => $clientWallet->id,
                'user_id' => $contract->client_id,
                'type' => 'escrow_release',
                'amount' => $milestone->amount,
                'fee' => 0,
                'reference_type' => 'ContractMilestone',
                'reference_id' => $milestone->id,
                'description' => "Escrow released for Milestone: {$milestone->title}",
                'status' => 'completed',
            ]);

            Transaction::create([
                'wallet_id' => $freelancerWallet->id,
                'user_id' => $contract->freelancer_id,
                'type' => 'deposit',
                'amount' => $freelancerReceives,
                'fee' => $platformFee,
                'reference_type' => 'ContractMilestone',
                'reference_id' => $milestone->id,
                'description' => "Payment received for Milestone: {$milestone->title} (Net after 10% fee)",
                'status' => 'completed',
            ]);

            // Send Payment Release Email to Freelancer
            try {
                $freelancer = $contract->freelancer;
                if ($freelancer && $freelancer->email) {
                    Mail::to($freelancer->email)->send(
                        new MarketplaceNotificationMail(
                            subject: '💰 Payment Released! $' . number_format($freelancerReceives, 2) . ' Credited to Your Wallet',
                            greeting: 'Hi ' . $freelancer->name . ',',
                            mainMessage: 'Payment has been released by ' . Auth::user()->name . ' for Milestone: "' . $milestone->title . '". Funds are now in your available wallet balance ready for withdrawal.',
                            actionUrl: route('wallet.index'),
                            actionText: 'View Wallet Balance',
                            details: [
                                'Milestone Value' => '$' . number_format($milestone->amount, 2),
                                'Platform Fee (10%)' => '-$' . number_format($platformFee, 2),
                                'Net Deposited' => '$' . number_format($freelancerReceives, 2),
                            ]
                        )
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Payment released email failed: ' . $e->getMessage());
            }

            return back()->with('success', 'Payment of $' . number_format($freelancerReceives, 2) . ' released to ' . $contract->freelancer->name . ' successfully!');
        });
    }

    public function completeContract(Contract $contract)
    {
        $user = Auth::user();
        if ($user->id !== $contract->client_id && $user->id !== $contract->freelancer_id && !$user->isAdmin()) {
            abort(403);
        }

        $contract->update([
            'status' => 'completed',
            'end_date' => now(),
        ]);

        $contract->freelancer->freelancerProfile?->increment('completed_jobs_count');
        $contract->jobPosting?->update(['status' => 'completed']);

        return back()->with('success', 'Contract marked as completed. Please leave a review for your collaborator!');
    }

    public function submitReview(Request $request, Contract $contract)
    {
        $user = Auth::user();
        if ($user->id !== $contract->client_id && $user->id !== $contract->freelancer_id) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'feedback' => 'required|string|min:10',
            'communication_rating' => 'nullable|numeric|min:1|max:5',
            'quality_rating' => 'nullable|numeric|min:1|max:5',
            'deadline_rating' => 'nullable|numeric|min:1|max:5',
        ]);

        $isClient = $user->id === $contract->client_id;
        $revieweeId = $isClient ? $contract->freelancer_id : $contract->client_id;
        $role = $isClient ? 'client_to_freelancer' : 'freelancer_to_client';

        Review::updateOrCreate([
            'contract_id' => $contract->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
        ], [
            'role' => $role,
            'rating' => (float) $validated['rating'],
            'communication_rating' => (float) ($validated['communication_rating'] ?? $validated['rating']),
            'quality_rating' => (float) ($validated['quality_rating'] ?? $validated['rating']),
            'deadline_rating' => (float) ($validated['deadline_rating'] ?? $validated['rating']),
            'feedback' => $validated['feedback'],
        ]);

        // Recalculate reviewee average rating
        $reviewee = User::find($revieweeId);
        if ($reviewee) {
            $avg = Review::where('reviewee_id', $revieweeId)->avg('rating');
            $count = Review::where('reviewee_id', $revieweeId)->count();
            $reviewee->update([
                'rating' => round($avg, 2),
                'rating_count' => $count,
            ]);
        }

        return back()->with('success', 'Thank you! Your feedback and ratings have been recorded.');
    }
}
