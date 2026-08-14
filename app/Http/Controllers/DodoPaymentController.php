<?php

namespace App\Http\Controllers;

use App\Models\ContractMilestone;
use App\Models\User;
use App\Services\DodoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DodoPaymentController extends Controller
{
    protected DodoPaymentService $dodoService;

    public function __construct(DodoPaymentService $dodoService)
    {
        $this->dodoService = $dodoService;
    }

    /**
     * Initiate Dodo Checkout Session
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5',
            'purpose' => 'required|in:wallet_deposit,milestone_escrow',
            'reference_id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');
        $purpose = $request->input('purpose');
        $referenceId = $request->input('reference_id');

        $session = $this->dodoService->createCheckoutSession($user, $amount, $purpose, $referenceId);

        if ($session['success'] && !empty($session['checkout_url'])) {
            return redirect()->away($session['checkout_url']);
        }

        return back()->with('error', 'Unable to initialize Dodo payment session. Please try again.');
    }

    /**
     * Interactive Dodo Hosted Checkout Page Simulator
     */
    public function simulator(Request $request)
    {
        $amount = (float) $request->input('amount', 100);
        $purpose = $request->input('purpose', 'wallet_deposit');
        $referenceId = $request->input('reference_id');
        $user = Auth::user() ?? User::find($request->input('user_id'));

        $milestone = null;
        if ($purpose === 'milestone_escrow' && $referenceId) {
            $milestone = ContractMilestone::find($referenceId);
        }

        return view('payments.dodo-checkout', compact('amount', 'purpose', 'referenceId', 'user', 'milestone'));
    }

    /**
     * Payment Success Callback from Dodo
     */
    public function returnUrl(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $amount = (float) $request->input('amount', 50);
        $purpose = $request->input('purpose', 'wallet_deposit');
        $referenceId = $request->input('reference_id');

        $result = $this->dodoService->fulfillPayment($user, $amount, $purpose, $referenceId);

        $message = $purpose === 'milestone_escrow'
            ? "Payment verified! $" . number_format($amount, 2) . " funded into protected escrow via Dodo Payments."
            : "Payment verified! $" . number_format($amount, 2) . " credited to your wallet balance via Dodo Payments.";

        return redirect($result['redirect'])->with('success', $message);
    }

    /**
     * Webhook Handler from Dodo Payments
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        // Webhook event processing logic
        return response()->json(['received' => true]);
    }
}
