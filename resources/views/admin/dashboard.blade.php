@extends('layouts.admin')

@section('content')
<div x-data="{
    activeModal: null,
    modalData: null,
    openModal(type, data) {
        this.activeModal = type;
        this.modalData = data;
    },
    closeModal() {
        this.activeModal = null;
        this.modalData = null;
    }
}" class="space-y-8">

    <!-- Hero Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Marketplace Super-Panel</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Real-time marketplace velocity, escrow funding flow, GMV metrics, and take-rate revenue.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Active Engine</span>
            </span>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Metric 1: GMV -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Gross Volume (GMV)</span>
                <span class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold">💳</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">${{ number_format($stats['total_volume'], 2) }}</span>
                <span class="text-[11px] text-emerald-400 font-semibold block mt-1">Cumulative contract volume</span>
            </div>
        </div>

        <!-- Metric 2: Revenue -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-emerald-900/50 shadow-xl flex flex-col justify-between hover:border-emerald-700/60 transition bg-gradient-to-b from-slate-900 to-emerald-950/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Platform Take-Rate (10%)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight">${{ number_format($stats['platform_revenue'], 2) }}</span>
                <span class="text-[11px] text-emerald-300/80 font-semibold block mt-1">Direct monetization share</span>
            </div>
        </div>

        <!-- Metric 3: Active Contracts -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Contracts</span>
                <span class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold">📜</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $stats['active_contracts'] }}</span>
                <span class="text-[11px] text-slate-400 font-semibold block mt-1">Escrows currently in-progress</span>
            </div>
        </div>

        <!-- Metric 4: Users -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Community & Talent</span>
                <span class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold">👥</span>
            </div>
            <div class="mt-4">
                <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $stats['total_users'] }}</span>
                <span class="text-[11px] text-slate-400 font-semibold block mt-1">
                    {{ $stats['total_freelancers'] }} Freelancers • {{ $stats['total_clients'] }} Clients
                </span>
            </div>
        </div>
    </div>

    <!-- Secondary Quick Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('admin.jobs') }}" class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Jobs</span>
                <span class="text-lg font-bold text-white mt-0.5 block">{{ $stats['total_jobs'] }}</span>
            </div>
            <span class="text-xs text-emerald-400">View &rarr;</span>
        </a>

        <a href="{{ route('admin.payouts') }}" class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Pending Payouts</span>
                <span class="text-lg font-bold {{ $stats['pending_payouts'] > 0 ? 'text-amber-400' : 'text-white' }} mt-0.5 block">{{ $stats['pending_payouts'] }}</span>
            </div>
            <span class="text-xs text-emerald-400">Audit &rarr;</span>
        </a>

        <a href="{{ route('admin.contracts') }}" class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Open Disputes</span>
                <span class="text-lg font-bold {{ $stats['open_disputes'] > 0 ? 'text-red-400' : 'text-slate-400' }} mt-0.5 block">{{ $stats['open_disputes'] }}</span>
            </div>
            <span class="text-xs text-emerald-400">Review &rarr;</span>
        </a>

        <a href="{{ route('admin.users') }}" class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-slate-700 transition flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Talent Roster</span>
                <span class="text-lg font-bold text-emerald-400 mt-0.5 block">{{ $stats['total_freelancers'] }}</span>
            </div>
            <span class="text-xs text-emerald-400">Explore &rarr;</span>
        </a>
    </div>

    <!-- Recent Activity Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        
        <!-- Recent Contracts Section -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-base">📜</span>
                    <h3 class="font-extrabold text-sm text-white">Recent Escrow Contracts</h3>
                </div>
                <a href="{{ route('admin.contracts') }}" class="text-xs font-bold text-emerald-400 hover:underline">View all ({{ $stats['active_contracts'] }}) &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentContracts as $c)
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-white block truncate text-xs sm:text-sm">{{ $c->title }}</span>
                            <div class="flex items-center gap-2 text-slate-400 text-[11px] mt-0.5 truncate">
                                <span>{{ $c->client->name }}</span>
                                <span class="text-slate-600">➔</span>
                                <span class="text-slate-300 font-medium">{{ $c->freelancer->name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                            <div class="text-left sm:text-right">
                                <span class="font-extrabold text-emerald-400 text-sm block">${{ number_format($c->amount, 2) }}</span>
                                <span class="text-[10px] text-slate-500 uppercase font-semibold block">{{ $c->status }}</span>
                            </div>
                            <button type="button" 
                                    @click="openModal('contract', {{ Js::from([
                                        'title' => $c->title,
                                        'amount' => number_format($c->amount, 2),
                                        'platform_fee' => number_format($c->amount * 0.10, 2),
                                        'status' => ucfirst($c->status),
                                        'client_name' => $c->client->name,
                                        'client_email' => $c->client->email,
                                        'freelancer_name' => $c->freelancer->name,
                                        'freelancer_email' => $c->freelancer->email,
                                        'created_at' => $c->created_at->format('M d, Y (h:i A)'),
                                        'public_url' => route('contracts.show', $c->id),
                                    ]) }})"
                                    class="px-2.5 py-1 rounded-lg bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-750 text-[11px] font-bold transition">
                                Inspect
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500 text-xs">No recent contracts available.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Ledger Transactions -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-base">Live Ledger</span>
                </div>
                <a href="{{ route('admin.payouts') }}" class="text-xs font-bold text-emerald-400 hover:underline">Manage Payouts &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($recentTransactions as $tx)
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-850 hover:border-slate-700 transition flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0 flex-1">
                            <span class="font-bold text-white block truncate">{{ $tx->description }}</span>
                            <span class="text-slate-400 text-[11px] block mt-0.5 truncate">{{ $tx->user->name ?? 'System' }} • {{ $tx->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-extrabold text-white text-sm block">${{ number_format($tx->amount, 2) }}</span>
                            <span class="text-[10px] text-emerald-400 uppercase font-semibold block">{{ $tx->type ?? 'Transaction' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-500 text-xs">No recent transaction entries.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Contract Detail Dialog Modal -->
    <div x-show="activeModal === 'contract'" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closeModal()">

        <div @click.outside="closeModal()" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full flex flex-col shadow-2xl overflow-hidden relative">

            <div class="p-5 border-b border-slate-800 bg-slate-950/70 flex items-start justify-between gap-4">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400" x-text="modalData?.status"></span>
                    <h3 class="text-lg font-extrabold text-white mt-1" x-text="modalData?.title"></h3>
                </div>
                <button @click="closeModal()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">✕</button>
            </div>

            <div class="p-5 space-y-4 text-xs text-slate-300">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Amount</span>
                        <span class="text-base font-extrabold text-white mt-0.5 block" x-text="'$' + modalData?.amount"></span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block">Platform 10% Fee</span>
                        <span class="text-base font-extrabold text-emerald-400 mt-0.5 block" x-text="'$' + modalData?.platform_fee"></span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Client:</span>
                        <span class="text-white font-bold" x-text="modalData?.client_name + ' (' + modalData?.client_email + ')'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Freelancer:</span>
                        <span class="text-white font-bold" x-text="modalData?.freelancer_name + ' (' + modalData?.freelancer_email + ')'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Created:</span>
                        <span class="text-slate-300" x-text="modalData?.created_at"></span>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between">
                <a :href="modalData?.public_url" target="_blank" class="text-xs text-emerald-400 hover:underline font-semibold">
                    Open Full Contract Workroom &rarr;
                </a>
                <button @click="closeModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

