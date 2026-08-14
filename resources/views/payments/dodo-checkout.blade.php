<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodo Payments Checkout — WorkForge</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased font-sans">
    <div class="w-full max-w-xl bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden" x-data="{
        paymentMethod: 'card',
        loading: false,
        submitPayment() {
            this.loading = true;
            setTimeout(() => {
                window.location.href = '{{ route('payments.dodo.return', [
                    'amount' => $amount,
                    'purpose' => $purpose,
                    'reference_id' => $referenceId,
                    'status' => 'success'
                ]) }}';
            }, 1200);
        }
    }">
        <!-- Top Dodo Banner -->
        <div class="p-6 bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-900 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-xl shadow-md">
                    🦤
                </div>
                <div>
                    <h2 class="font-extrabold text-white text-base tracking-tight">Dodo Payments</h2>
                    <span class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Secure Merchant of Record Checkout
                    </span>
                </div>
            </div>
            <span class="text-xs px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 font-bold">
                {{ config('services.dodo.environment') === 'live_mode' ? 'LIVE' : 'SANDBOX' }}
            </span>
        </div>

        <!-- Checkout Content -->
        <div class="p-6 sm:p-8 space-y-6">
            <!-- Order Summary Box -->
            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 uppercase tracking-wider block">Item Description</span>
                    <h3 class="text-sm font-bold text-white mt-0.5">
                        @if($purpose === 'milestone_escrow')
                            Milestone Escrow Funding: {{ $milestone->title ?? 'Contract Milestone #' . $referenceId }}
                        @else
                            Marketplace Wallet Balance Deposit
                        @endif
                    </h3>
                    <span class="text-xs text-slate-400 block mt-0.5">Buyer: {{ $user->name ?? 'Customer' }} ({{ $user->email ?? 'buyer@test.com' }})</span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 block">Total Due</span>
                    <span class="text-2xl font-black text-emerald-400">${{ number_format($amount, 2) }}</span>
                </div>
            </div>

            <!-- Payment Method Selector -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Select Payment Method</label>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'bg-emerald-600/20 border-emerald-500 text-white font-bold' : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:text-white'" class="p-3 rounded-2xl border text-xs text-center transition">
                        💳 Card
                    </button>
                    <button type="button" @click="paymentMethod = 'apple'" :class="paymentMethod === 'apple' ? 'bg-emerald-600/20 border-emerald-500 text-white font-bold' : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:text-white'" class="p-3 rounded-2xl border text-xs text-center transition">
                         Apple / GPay
                    </button>
                    <button type="button" @click="paymentMethod = 'upi'" :class="paymentMethod === 'upi' ? 'bg-emerald-600/20 border-emerald-500 text-white font-bold' : 'bg-slate-800/60 border-slate-700 text-slate-400 hover:text-white'" class="p-3 rounded-2xl border text-xs text-center transition">
                        ⚡ UPI / Local
                    </button>
                </div>
            </div>

            <!-- Simulated Card Input Form -->
            <div x-show="paymentMethod === 'card'" class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Card Number</label>
                    <input type="text" value="•••• •••• •••• 4242" readonly class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Expires</label>
                        <input type="text" value="12 / 28" readonly class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">CVC</label>
                        <input type="text" value="•••" readonly class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-sm text-white">
                    </div>
                </div>
            </div>

            <div x-show="paymentMethod === 'apple'" class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700 text-center text-xs text-slate-300">
                <span> Pay or Google Pay authentication will prompt upon clicking Pay.</span>
            </div>

            <div x-show="paymentMethod === 'upi'" class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700 text-center text-xs text-slate-300">
                <span>Scan QR code with any UPI app (GPay, PhonePe, Paytm) or European iDEAL / SEPA bank.</span>
            </div>

            <!-- Pay Button -->
            <button type="button" @click="submitPayment" :disabled="loading" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-slate-950 font-black text-sm rounded-2xl shadow-lg transition flex items-center justify-center gap-2">
                <span x-show="!loading">Pay ${{ number_format($amount, 2) }} with Dodo Payments &rarr;</span>
                <span x-show="loading" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Processing Secure Payment...
                </span>
            </button>

            <!-- Footer / Security Details -->
            <div class="text-center pt-2 border-t border-slate-800 text-[11px] text-slate-500 space-y-1">
                <p>🔒 256-Bit SSL Encrypted • Powered by Dodo Payments Merchant of Record</p>
                <p>All taxes, VAT and compliance automatically handled by Dodo Payments Inc.</p>
            </div>
        </div>
    </div>
</body>
</html>
