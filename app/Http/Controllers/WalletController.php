<?php

namespace App\Http\Controllers;

use App\Models\PayoutRequest;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);
        $transactions = Transaction::where('user_id', $user->id)->latest()->paginate(15);
        $payoutRequests = PayoutRequest::where('user_id', $user->id)->latest()->get();

        return view('wallet.index', compact('wallet', 'transactions', 'payoutRequests'));
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10|max:100000',
        ]);

        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $wallet->balance += (float) $validated['amount'];
        $wallet->save();

        Transaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => (float) $validated['amount'],
            'fee' => 0.00,
            'description' => 'Demo account instant funds deposit',
            'status' => 'completed',
        ]);

        // Send Email Receipt
        try {
            if ($user->email) {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\MarketplaceNotificationMail(
                        subject: '💳 Wallet Deposit Confirmation ($' . number_format($validated['amount'], 2) . ')',
                        greeting: 'Hello ' . $user->name . ',',
                        mainMessage: 'Your deposit of $' . number_format($validated['amount'], 2) . ' has been credited to your WorkForge wallet balance.',
                        actionUrl: route('wallet.index'),
                        actionText: 'View Wallet Balance',
                        details: [
                            'Deposited' => '$' . number_format($validated['amount'], 2),
                            'New Balance' => '$' . number_format($wallet->balance, 2),
                            'Status' => 'Completed',
                        ]
                    )
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Deposit email failed: ' . $e->getMessage());
        }

        return back()->with('success', '$' . number_format($validated['amount'], 2) . ' deposited into your wallet successfully.');
    }

    public function requestPayout(Request $request)
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:20|max:' . $wallet->balance,
            'payout_method' => 'required|in:bank_transfer,paypal,stripe_connect,crypto',
            'account_email' => 'nullable|string',
            'account_number' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];

        $wallet->balance -= $amount;
        $wallet->save();

        $payout = PayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'payout_method' => $validated['payout_method'],
            'account_details' => [
                'email' => $validated['account_email'] ?? $user->email,
                'account' => $validated['account_number'] ?? 'ACCT-'.rand(100000, 999999),
            ],
            'status' => 'pending',
        ]);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'type' => 'payout',
            'amount' => $amount,
            'fee' => 0.00,
            'reference_type' => 'PayoutRequest',
            'reference_id' => $payout->id,
            'description' => "Withdrawal request ({$validated['payout_method']})",
            'status' => 'pending',
        ]);

        return back()->with('success', 'Withdrawal request of $' . number_format($amount, 2) . ' submitted for processing.');
    }
}
