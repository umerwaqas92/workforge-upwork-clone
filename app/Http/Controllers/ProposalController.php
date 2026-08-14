<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\JobPosting;
use App\Models\Message;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    public function create(JobPosting $job)
    {
        if (!Auth::user()->isFreelancer()) {
            return redirect()->back()->with('error', 'Only freelancers can submit proposals.');
        }

        // Check if already applied
        $existing = Proposal::where('job_posting_id', $job->id)
            ->where('freelancer_id', Auth::id())
            ->first();

        if ($existing) {
            return redirect()->route('proposals.show', $existing->id)->with('info', 'You have already submitted a proposal for this job.');
        }

        return view('proposals.create', compact('job'));
    }

    public function store(Request $request, JobPosting $job)
    {
        if (!Auth::user()->isFreelancer()) {
            return redirect()->back()->with('error', 'Only freelancers can submit proposals.');
        }

        $validated = $request->validate([
            'bid_amount' => 'required|numeric|min:5',
            'delivery_time_days' => 'nullable|integer|min:1',
            'cover_letter' => 'required|string|min:20',
            'milestone_titles' => 'nullable|array',
            'milestone_amounts' => 'nullable|array',
        ]);

        $feePercent = 0.10; // 10% platform fee
        $bidAmount = (float) $validated['bid_amount'];
        $platformFee = round($bidAmount * $feePercent, 2);
        $receiveAmount = round($bidAmount - $platformFee, 2);

        $milestones = [];
        if (!empty($validated['milestone_titles'])) {
            foreach ($validated['milestone_titles'] as $idx => $mTitle) {
                if (!empty($mTitle)) {
                    $milestones[] = [
                        'title' => $mTitle,
                        'amount' => (float) ($validated['milestone_amounts'][$idx] ?? ($bidAmount / count($validated['milestone_titles']))),
                    ];
                }
            }
        }

        $proposal = Proposal::create([
            'job_posting_id' => $job->id,
            'freelancer_id' => Auth::id(),
            'bid_amount' => $bidAmount,
            'platform_fee' => $platformFee,
            'receive_amount' => $receiveAmount,
            'delivery_time_days' => $validated['delivery_time_days'] ?? 14,
            'cover_letter' => $validated['cover_letter'],
            'milestones' => !empty($milestones) ? $milestones : null,
            'status' => 'pending',
        ]);

        $job->increment('proposals_count');

        // Send email to job client
        try {
            $client = $job->client;
            if ($client && $client->email) {
                \Illuminate\Support\Facades\Mail::to($client->email)->send(
                    new \App\Mail\MarketplaceNotificationMail(
                        subject: '📬 New Proposal on "' . $job->title . '"',
                        greeting: 'Hello ' . $client->name . ',',
                        mainMessage: Auth::user()->name . ' has submitted a proposal for your project "' . $job->title . '".',
                        actionUrl: route('proposals.show', $proposal->id),
                        actionText: 'Review Proposal & Bid',
                        details: [
                            'Freelancer' => Auth::user()->name,
                            'Bid Amount' => '$' . number_format($bidAmount, 2),
                            'Est. Delivery' => ($validated['delivery_time_days'] ?? 14) . ' days',
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Proposal email failed: ' . $e->getMessage());
        }

        return redirect()->route('proposals.show', $proposal->id)->with('success', 'Proposal submitted successfully!');
    }

    public function show(Proposal $proposal)
    {
        // Must be client owner, proposal freelancer, or admin
        $user = Auth::user();
        if ($user->id !== $proposal->freelancer_id && $user->id !== $proposal->jobPosting->client_id && !$user->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($user->id === $proposal->jobPosting->client_id && !$proposal->client_seen) {
            $proposal->update(['client_seen' => true]);
        }

        $proposal->load(['jobPosting.client.clientProfile', 'freelancer.freelancerProfile', 'freelancer.skills']);

        return view('proposals.show', compact('proposal'));
    }

    public function updateStatus(Request $request, Proposal $proposal)
    {
        $job = $proposal->jobPosting;
        if (Auth::id() !== $job->client_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:shortlisted,rejected,pending']);
        $proposal->update(['status' => $request->status]);

        return back()->with('success', 'Proposal status updated to ' . ucfirst($request->status));
    }
}
