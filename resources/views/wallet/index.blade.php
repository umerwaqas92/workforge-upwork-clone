@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ depositModal: false, withdrawModal: false, depositMethod: 'dodo' }">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Wallet & Transaction Ledger</h1>
            <p class="text-sm text-slate-500 mt-1">Manage escrow deposits, available funds, and withdrawal requests.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="depositModal = true" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-2">
                <span>🦤</span>
                <span>+ Deposit with Dodo Payments</span>
            </button>
            <button @click="withdrawModal = true" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                Withdraw Earnings
            </button>
        </div>
    </div>

    <!-- Balance Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Available Balance</span>
            <span class="text-3xl font-extrabold text-slate-900">${{ number_format($wallet->balance, 2) }}</span>
            <p class="text-xs text-slate-400 mt-2">Ready for withdrawal or new contract funding</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block mb-1">Protected in Escrow</span>
            <span class="text-3xl font-extrabold text-emerald-700">${{ number_format($wallet->escrow_balance, 2) }}</span>
            <p class="text-xs text-slate-400 mt-2">Held securely until milestones are approved</p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pending Withdrawals</span>
            <span class="text-3xl font-extrabold text-slate-700">
                ${{ number_format($payoutRequests->where('status', 'pending')->sum('amount'), 2) }}
            </span>
            <p class="text-xs text-slate-400 mt-2">Processing within 24-48 hours</p>
        </div>
    </div>

    <!-- Transactions Ledger Table -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Transaction History</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                        <th class="pb-3 font-semibold">Date</th>
                        <th class="pb-3 font-semibold">Type</th>
                        <th class="pb-3 font-semibold">Description</th>
                        <th class="pb-3 font-semibold">Amount</th>
                        <th class="pb-3 font-semibold">Fee</th>
                        <th class="pb-3 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 text-slate-500 font-medium">{{ $tx->created_at->format('M d, Y h:i A') }}</td>
                            <td class="py-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ in_array($tx->type, ['deposit', 'escrow_release']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', $tx->type) }}
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-800 font-medium">{{ $tx->description }}</td>
                            <td class="py-3.5 font-bold {{ in_array($tx->type, ['deposit', 'escrow_release']) ? 'text-emerald-700' : 'text-slate-900' }}">
                                {{ in_array($tx->type, ['deposit', 'escrow_release']) ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="py-3.5 text-slate-400">${{ number_format($tx->fee, 2) }}</td>
                            <td class="py-3.5 text-right font-bold text-emerald-700 uppercase tracking-wider text-[10px]">
                                {{ $tx->status }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">No transactions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Deposit Modal with Dodo Payments -->
    <div x-show="depositModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-6" @click.away="depositModal = false">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-lg">
                    🦤
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Deposit Funds</h3>
                    <p class="text-xs text-slate-500">Powered by Dodo Payments (Cards, Apple Pay, UPI)</p>
                </div>
            </div>

            <!-- Dodo Checkout Form -->
            <form action="{{ route('payments.dodo.checkout') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="purpose" value="wallet_deposit">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deposit Amount ($) *</label>
                    <input type="number" step="10" min="10" name="amount" value="500" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xl font-black text-slate-900 focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Quick Presets -->
                <div class="grid grid-cols-4 gap-2" x-data="{ amt: 500 }">
                    <button type="button" @click="$el.closest('form').querySelector('input[name=amount]').value = 100" class="py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">$100</button>
                    <button type="button" @click="$el.closest('form').querySelector('input[name=amount]').value = 250" class="py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">$250</button>
                    <button type="button" @click="$el.closest('form').querySelector('input[name=amount]').value = 500" class="py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">$500</button>
                    <button type="button" @click="$el.closest('form').querySelector('input[name=amount]').value = 1500" class="py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">$1,500</button>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 space-y-1">
                    <p class="font-semibold text-slate-700">🔒 Merchant of Record Protection</p>
                    <p>Includes automated VAT, currency conversion, and fraud shielding.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="depositModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-1.5">
                        <span>Continue to Dodo Checkout &rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div x-show="withdrawModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-6" @click.away="withdrawModal = false">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Withdraw Available Earnings</h3>
                <p class="text-xs text-slate-500 mt-1">Available balance: <strong>${{ number_format($wallet->balance, 2) }}</strong></p>
            </div>

            <form action="{{ route('wallet.payout') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Withdrawal Amount ($) *</label>
                        <span class="text-[11px] text-slate-400">Min: ${{ number_format(\App\Models\PlatformSetting::get('min_payout_amount', 50.0), 2) }}</span>
                    </div>
                    <input type="number" step="0.01" min="{{ \App\Models\PlatformSetting::get('min_payout_amount', 50.0) }}" max="{{ $wallet->balance }}" name="amount" value="{{ max(\App\Models\PlatformSetting::get('min_payout_amount', 50.0), min(500, $wallet->balance)) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-lg font-bold">
                    <p class="text-[11px] text-slate-400 mt-1">Platform payout processing fee: <strong>${{ number_format(\App\Models\PlatformSetting::get('payout_fixed_fee', 1.50), 2) }}</strong></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Payout Method *</label>
                    <select name="payout_method" required class="w-full text-xs rounded-xl border-slate-300 py-2.5">
                        <option value="dodo_payout">Dodo Payments Direct Global Wire</option>
                        <option value="bank_transfer">Direct Bank Transfer (ACH / Wire)</option>
                        <option value="paypal">PayPal Express</option>
                        <option value="crypto">USDC Crypto Wallet</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Account / Email Details</label>
                    <input type="text" name="account_email" value="{{ Auth::user()->email }}" placeholder="paypal@domain.com or Bank IBAN" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="withdrawModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow">
                        Request Payout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
