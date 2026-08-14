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
            'platform_revenue' => Contract::sum('amount') * 0.10, // 10% platform take-rate
            'pending_payouts' => PayoutRequest::where('status', 'pending')->count(),
            'open_disputes' => Dispute::where('status', 'opened')->count(),
        ];

        $recentUsers = User::latest()->take(6)->get();
        $recentContracts = Contract::with(['client', 'freelancer'])->latest()->take(6)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentContracts', 'recentTransactions'));
    }

    public function users()
    {
        $users = User::with(['freelancerProfile', 'clientProfile', 'wallet'])->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function jobs()
    {
        $jobs = JobPosting::with(['client', 'category'])->latest()->paginate(15);
        return view('admin.jobs', compact('jobs'));
    }

    public function contracts()
    {
        $contracts = Contract::with(['client', 'freelancer', 'jobPosting'])->latest()->paginate(15);
        return view('admin.contracts', compact('contracts'));
    }

    public function payouts()
    {
        $payouts = PayoutRequest::with('user')->latest()->paginate(15);
        return view('admin.payouts', compact('payouts'));
    }

    public function approvePayout(PayoutRequest $payout)
    {
        $payout->update([
            'status' => 'processed',
            'processed_at' => now(),
            'admin_notes' => 'Approved and transferred via admin dashboard',
        ]);

        return back()->with('success', 'Payout marked as processed successfully.');
    }
}
