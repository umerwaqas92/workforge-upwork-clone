@extends('layouts.admin')

@section('content')
<div x-data="{
    activePayout: null,
    showModal: false,
    openPayout(payout) {
        this.activePayout = payout;
        this.showModal = true;
    },
    closePayout() {
        this.showModal = false;
        this.activePayout = null;
    }
}" class="space-y-6">

    <!-- Header & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Payout Requests & Financial Settlement</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Audit freelancer withdrawal requests, verify payment account credentials, and release funds.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300">
                Total: <span class="text-emerald-400">{{ $payouts->total() }}</span> Requests
            </span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.payouts') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Search Freelancer</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Payout Method</label>
                <select name="method" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Methods</option>
                    <option value="dodo_payout" {{ request('method') === 'dodo_payout' ? 'selected' : '' }}>Dodo Payments</option>
                    <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="paypal" {{ request('method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="stripe_connect" {{ request('method') === 'stripe_connect' ? 'selected' : '' }}>Stripe</option>
                    <option value="crypto" {{ request('method') === 'crypto' ? 'selected' : '' }}>Crypto</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-950/40 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'method']))
                    <a href="{{ route('admin.payouts') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 px-3 rounded-xl text-xs transition" title="Clear Filters">✕</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Responsive Table Card -->
    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs text-slate-300 min-w-[750px]">
                <thead>
                    <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase font-bold text-[11px] tracking-wider">
                        <th class="py-3.5 px-5">Freelancer</th>
                        <th class="py-3.5 px-4">Amount Requested</th>
                        <th class="py-3.5 px-4">Method</th>
                        <th class="py-3.5 px-4">Requested Date</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($payouts as $p)
                        <tr class="hover:bg-slate-850 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $p->user->avatar_url }}" alt="{{ $p->user->name }}" class="w-8 h-8 rounded-lg object-cover">
                                    <div>
                                        <span class="font-bold text-white block">{{ $p->user->name }}</span>
                                        <span class="text-slate-400 text-[11px]">{{ $p->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-emerald-400 text-sm whitespace-nowrap">
                                ${{ number_format($p->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-200 uppercase">
                                    {{ str_replace('_', ' ', $p->payout_method) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 whitespace-nowrap">
                                {{ $p->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $p->status === 'pending' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : ($p->status === 'processed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button"
                                            @click="openPayout({{ Js::from([
                                                'id' => $p->id,
                                                'amount' => number_format($p->amount, 2),
                                                'payout_method' => str_replace('_', ' ', $p->payout_method),
                                                'account_details' => $p->account_details,
                                                'status' => $p->status,
                                                'admin_notes' => $p->admin_notes ?? '',
                                                'created_at_formatted' => $p->created_at->format('M d, Y (h:i A)'),
                                                'processed_at_formatted' => $p->processed_at ? $p->processed_at->format('M d, Y (h:i A)') : 'Not yet processed',
                                                'user' => [
                                                    'name' => $p->user->name,
                                                    'email' => $p->user->email,
                                                    'avatar_url' => $p->user->avatar_url,
                                                    'country' => $p->user->country ?? 'Worldwide',
                                                    'wallet_balance' => number_format($p->user->wallet->balance ?? 0, 2),
                                                ],
                                                'approve_route' => route('admin.payouts.approve', $p->id),
                                                'status_route' => route('admin.payouts.status', $p->id),
                                            ]) }})"
                                            class="px-3 py-1.5 rounded-xl bg-slate-850 hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold border border-slate-750 hover:border-emerald-500 text-xs transition inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Details</span>
                                    </button>

                                    @if($p->status === 'pending')
                                        <form action="{{ route('admin.payouts.approve', $p->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="space-y-2">
                                    <span class="text-3xl block">💰</span>
                                    <span class="text-sm font-semibold block">No payout requests found matching your query.</span>
                                    <a href="{{ route('admin.payouts') }}" class="text-xs text-emerald-400 hover:underline">Reset search filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>

    <!-- Payout Details Interactive Dialog Modal -->
    <div x-show="showModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closePayout()">

        <div @click.outside="closePayout()" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">

            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-800 bg-slate-950/70 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-800 text-slate-200" x-text="activePayout?.payout_method"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                              :class="{
                                  'bg-amber-500/20 text-amber-400 border border-amber-500/30': activePayout?.status === 'pending',
                                  'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activePayout?.status === 'processed',
                                  'bg-red-500/20 text-red-400 border border-red-500/30': activePayout?.status === 'rejected'
                              }"
                              x-text="activePayout?.status"></span>
                    </div>
                    <h3 class="text-xl font-extrabold text-white" x-text="'Withdrawal of $' + activePayout?.amount"></h3>
                </div>

                <button @click="closePayout()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-6 text-xs text-slate-300">
                
                <!-- Freelancer Info Card -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img :src="activePayout?.user.avatar_url" :alt="activePayout?.user.name" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-700 shrink-0">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Requested By</span>
                            <span class="text-sm font-bold text-white block truncate" x-text="activePayout?.user.name"></span>
                            <span class="text-[11px] text-slate-400 block truncate" x-text="activePayout?.user.email"></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Remaining Wallet</span>
                        <span class="text-xs font-bold text-white block" x-text="'$' + activePayout?.user.wallet_balance"></span>
                    </div>
                </div>

                <!-- Account Credentials Details -->
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Destination Account Credentials</span>
                    <div class="bg-slate-900 p-3.5 rounded-xl border border-slate-800/80 space-y-2">
                        <template x-if="typeof activePayout?.account_details === 'object' && activePayout?.account_details !== null">
                            <div class="space-y-1.5">
                                <template x-for="(val, key) in activePayout.account_details" :key="key">
                                    <div class="flex items-center justify-between text-xs py-1 border-b border-slate-800/40 last:border-0">
                                        <span class="text-slate-400 uppercase font-semibold text-[10px]" x-text="key.replace(/_/g, ' ')"></span>
                                        <span class="text-white font-mono font-bold" x-text="val"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="typeof activePayout?.account_details !== 'object' || activePayout?.account_details === null">
                            <p class="font-mono text-slate-300" x-text="activePayout?.account_details || 'No account payload provided'"></p>
                        </template>
                    </div>
                </div>

                <!-- Timestamps & Audit Log -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Submission Date</span>
                        <span class="text-xs font-bold text-white mt-0.5 block" x-text="activePayout?.created_at_formatted"></span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Processed Date</span>
                        <span class="text-xs font-bold text-white mt-0.5 block" x-text="activePayout?.processed_at_formatted"></span>
                    </div>
                </div>

                <!-- Status & Notes Moderation Form -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-300">Admin Resolution & Notes</h4>
                    <form :action="activePayout?.status_route" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Update Status</label>
                                <select name="status" class="w-full bg-slate-900 border border-slate-750 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                                    <option value="pending" :selected="activePayout?.status === 'pending'">Pending Approval</option>
                                    <option value="processed" :selected="activePayout?.status === 'processed'">Processed & Paid Out</option>
                                    <option value="rejected" :selected="activePayout?.status === 'rejected'">Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Admin Audit Notes</label>
                                <input type="text" name="admin_notes" :value="activePayout?.admin_notes" placeholder="Transaction ref / transfer ID..." 
                                       class="w-full bg-slate-900 border border-slate-750 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-md">
                                Save Payout Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between gap-3">
                <div>
                    <template x-if="activePayout?.status === 'pending'">
                        <form :action="activePayout?.approve_route" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md">
                                Approve & Release Instantly
                            </button>
                        </form>
                    </template>
                </div>
                <button @click="closePayout()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    Close Dialog
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

