<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contract;
use App\Models\Dispute;
use App\Models\JobPosting;
use App\Models\PayoutRequest;
use App\Models\Proposal;
use App\Models\Skill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_freelancers' => User::where('role', 'freelancer')->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'total_jobs' => JobPosting::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'total_volume' => Contract::sum('amount'),
            'platform_revenue' => Contract::sum('amount') * (\App\Models\PlatformSetting::get('platform_fee_percent', 10.0) / 100),
            'pending_payouts' => PayoutRequest::where('status', 'pending')->count(),
            'open_disputes' => Dispute::where('status', 'opened')->count(),
        ];

        $recentUsers = User::with(['freelancerProfile', 'clientProfile', 'wallet', 'skills'])->latest()->take(6)->get();
        $recentContracts = Contract::with(['client.clientProfile', 'freelancer.freelancerProfile', 'jobPosting', 'milestones'])->latest()->take(6)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(8)->get();
        $recentJobs = JobPosting::with(['client', 'category', 'skills'])->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentContracts', 'recentTransactions', 'recentJobs'));
    }

    public function users(Request $request)
    {
        $query = User::with(['freelancerProfile', 'clientProfile', 'wallet', 'skills']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,pending',
        ]);

        $user->update(['status' => $request->input('status')]);

        return back()->with('success', "User '{$user->name}' status updated to " . ucfirst($request->input('status')) . ".");
    }

    public function jobs(Request $request)
    {
        $query = JobPosting::with(['client.clientProfile', 'category', 'skills', 'proposals']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();
        return view('admin.jobs', compact('jobs'));
    }

    public function updateJobStatus(Request $request, JobPosting $job)
    {
        $request->validate([
            'status' => 'required|in:draft,open,in_progress,completed,closed',
        ]);

        $job->update(['status' => $request->input('status')]);

        return back()->with('success', "Job posting '{$job->title}' status updated to " . ucfirst(str_replace('_', ' ', $request->input('status'))) . ".");
    }

    public function contracts(Request $request)
    {
        $query = Contract::with(['client.clientProfile', 'freelancer.freelancerProfile', 'jobPosting', 'milestones']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('freelancer', function ($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $contracts = $query->latest()->paginate(15)->withQueryString();
        return view('admin.contracts', compact('contracts'));
    }

    public function payouts(Request $request)
    {
        $query = PayoutRequest::with(['user.wallet', 'user.freelancerProfile']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('method')) {
            $query->where('payout_method', $request->input('method'));
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();
        return view('admin.payouts', compact('payouts'));
    }

    public function updatePayoutStatus(Request $request, PayoutRequest $payout)
    {
        $request->validate([
            'status' => 'required|in:pending,processed,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $status = $request->input('status');
        $oldStatus = $payout->status;

        $payout->update([
            'status' => $status,
            'admin_notes' => $request->input('admin_notes', $payout->admin_notes),
            'processed_at' => $status === 'processed' ? now() : null,
        ]);

        // Synchronize related ledger transaction
        $tx = Transaction::where(function ($q) use ($payout) {
            $q->where('reference_type', 'PayoutRequest')->where('reference_id', $payout->id);
        })->orWhere(function ($q) use ($payout) {
            $q->where('user_id', $payout->user_id)
              ->where('type', 'payout')
              ->where('amount', $payout->amount)
              ->where('status', 'pending');
        })->latest()->first();

        if ($tx) {
            if ($status === 'processed') {
                $tx->update(['status' => 'completed']);
            } elseif ($status === 'rejected') {
                $tx->update(['status' => 'failed', 'description' => $tx->description . ' (Rejected)']);
            } else {
                $tx->update(['status' => 'pending']);
            }
        }

        // If rejected and wasn't already rejected, refund funds back to freelancer wallet
        if ($status === 'rejected' && $oldStatus !== 'rejected') {
            $wallet = $payout->user->wallet;
            if ($wallet) {
                $wallet->balance += $payout->amount;
                $wallet->save();
            }
        }

        return back()->with('success', "Payout request status updated to " . ucfirst($status) . " and ledger synchronized.");
    }

    public function approvePayout(PayoutRequest $payout)
    {
        $payout->update([
            'status' => 'processed',
            'processed_at' => now(),
            'admin_notes' => 'Approved and transferred via admin dashboard',
        ]);

        // Synchronize related ledger transaction to completed
        $tx = Transaction::where(function ($q) use ($payout) {
            $q->where('reference_type', 'PayoutRequest')->where('reference_id', $payout->id);
        })->orWhere(function ($q) use ($payout) {
            $q->where('user_id', $payout->user_id)
              ->where('type', 'payout')
              ->where('amount', $payout->amount)
              ->where('status', 'pending');
        })->latest()->first();

        if ($tx) {
            $tx->update(['status' => 'completed']);
        }

        return back()->with('success', 'Payout marked as processed and transaction ledger updated to Completed.');
    }

    public function settings()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('platform_settings')) {
            \Illuminate\Support\Facades\Schema::create('platform_settings', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('string');
                $table->string('group')->default('monetization');
                $table->string('label');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        \App\Models\PlatformSetting::seedDefaults();
        $settings = \App\Models\PlatformSetting::all()->groupBy('group');

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            \App\Models\PlatformSetting::set($key, $value);
        }

        // If badge thresholds were modified, trigger recalculation
        \App\Models\FreelancerProfile::with('user')->get()->each->recalculateBadgeStatus();

        return back()->with('success', 'Platform settings updated successfully and cached globally!');
    }
}
