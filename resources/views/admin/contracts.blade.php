@extends('layouts.admin')

@section('content')
<div x-data="{
    activeContract: null,
    showModal: false,
    openContract(contract) {
        this.activeContract = contract;
        this.showModal = true;
    },
    closeContract() {
        this.showModal = false;
        this.activeContract = null;
    }
}" class="space-y-6">

    <!-- Header & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Contracts & Escrow Oversight</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Audit active engagements, escrow milestone funding, platform take-rate, and disbursements.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300">
                Total: <span class="text-emerald-400">{{ $contracts->total() }}</span> Contracts
            </span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.contracts') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Search Contract / Parties</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, client, or freelancer name..." 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="disputed" {{ request('status') === 'disputed' ? 'selected' : '' }}>Disputed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-950/40 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.contracts') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 px-3 rounded-xl text-xs transition" title="Clear Filters">✕</a>
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
                        <th class="py-3.5 px-5">Contract</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Freelancer</th>
                        <th class="py-3.5 px-4">Total Amount</th>
                        <th class="py-3.5 px-4">Platform Fee (10%)</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($contracts as $c)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-5 max-w-xs">
                                <span class="font-bold text-white block truncate text-xs sm:text-sm">{{ $c->title }}</span>
                                <span class="text-slate-500 text-[11px] block mt-0.5">{{ $c->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $c->client->avatar_url }}" alt="{{ $c->client->name }}" class="w-6 h-6 rounded-lg object-cover">
                                    <span class="text-slate-300 font-medium">{{ $c->client->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $c->freelancer->avatar_url }}" alt="{{ $c->freelancer->name }}" class="w-6 h-6 rounded-lg object-cover">
                                    <span class="text-slate-300 font-medium">{{ $c->freelancer->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-white whitespace-nowrap">
                                ${{ number_format($c->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-emerald-400 font-bold whitespace-nowrap">
                                ${{ number_format($c->amount * 0.10, 2) }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $c->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($c->status === 'completed' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : ($c->status === 'disputed' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700')) }}">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <button type="button"
                                        @click="openContract({{ Js::from([
                                            'id' => $c->id,
                                            'title' => $c->title,
                                            'type' => ucfirst(str_replace('_', ' ', $c->type ?? 'fixed_price')),
                                            'amount' => number_format($c->amount, 2),
                                            'platform_fee' => number_format($c->amount * 0.10, 2),
                                            'freelancer_net' => number_format($c->amount * 0.90, 2),
                                            'total_paid' => number_format($c->total_paid, 2),
                                            'total_escrow' => number_format($c->total_escrow, 2),
                                            'status' => $c->status,
                                            'status_formatted' => ucfirst($c->status),
                                            'terms' => $c->terms ?? 'Standard marketplace escrow contract terms apply.',
                                            'created_at_formatted' => $c->created_at->format('M d, Y (h:i A)'),
                                            'start_date' => $c->start_date ? $c->start_date->format('M d, Y') : 'Immediate',
                                            'end_date' => $c->end_date ? $c->end_date->format('M d, Y') : 'Ongoing',
                                            'client' => [
                                                'name' => $c->client->name,
                                                'email' => $c->client->email,
                                                'avatar_url' => $c->client->avatar_url,
                                                'company' => $c->client->clientProfile->company_name ?? 'Individual Client',
                                                'country' => $c->client->country ?? 'Worldwide',
                                            ],
                                            'freelancer' => [
                                                'name' => $c->freelancer->name,
                                                'email' => $c->freelancer->email,
                                                'avatar_url' => $c->freelancer->avatar_url,
                                                'title' => $c->freelancer->freelancerProfile->title ?? 'Talent',
                                                'country' => $c->freelancer->country ?? 'Worldwide',
                                            ],
                                            'milestones' => $c->milestones->map(fn($m) => [
                                                'id' => $m->id,
                                                'title' => $m->title,
                                                'amount' => number_format($m->amount, 2),
                                                'status' => ucfirst(str_replace('_', ' ', $m->status)),
                                                'status_raw' => $m->status,
                                                'due_date' => $m->due_date ? $m->due_date->format('M d, Y') : 'No deadline',
                                            ]),
                                            'public_url' => route('contracts.show', $c->id),
                                        ]) }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-850 hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold border border-slate-750 hover:border-emerald-500 text-xs transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Inspect Contract</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="space-y-2">
                                    <span class="text-3xl block">📜</span>
                                    <span class="text-sm font-semibold block">No contracts found matching your query.</span>
                                    <a href="{{ route('admin.contracts') }}" class="text-xs text-emerald-400 hover:underline">Reset search filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

    <!-- Contract Details Dialog Modal -->
    <div x-show="showModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closeContract()">

        <div @click.outside="closeContract()" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">

            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-800 bg-slate-950/70 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                              :class="{
                                  'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeContract?.status === 'active',
                                  'bg-blue-500/20 text-blue-400 border border-blue-500/30': activeContract?.status === 'completed',
                                  'bg-red-500/20 text-red-400 border border-red-500/30': activeContract?.status === 'disputed',
                                  'bg-slate-800 text-slate-400 border border-slate-700': activeContract?.status === 'cancelled' || activeContract?.status === 'paused'
                              }"
                              x-text="activeContract?.status_formatted"></span>
                        <span class="text-[11px] text-slate-400" x-text="'Started ' + activeContract?.start_date"></span>
                    </div>
                    <h3 class="text-base sm:text-xl font-extrabold text-white leading-tight" x-text="activeContract?.title"></h3>
                </div>

                <button @click="closeContract()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-6 text-xs text-slate-300">
                
                <!-- Client & Freelancer Parties Card -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <img :src="activeContract?.client.avatar_url" :alt="activeContract?.client.name" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-700 shrink-0">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase text-blue-400 block">Hiring Client</span>
                            <span class="text-xs sm:text-sm font-bold text-white block truncate" x-text="activeContract?.client.name"></span>
                            <span class="text-[11px] text-slate-400 truncate block" x-text="activeContract?.client.email"></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <img :src="activeContract?.freelancer.avatar_url" :alt="activeContract?.freelancer.name" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-700 shrink-0">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase text-emerald-400 block">Freelancer Expert</span>
                            <span class="text-xs sm:text-sm font-bold text-white block truncate" x-text="activeContract?.freelancer.name"></span>
                            <span class="text-[11px] text-slate-400 truncate block" x-text="activeContract?.freelancer.title"></span>
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown Matrix -->
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Financial & Escrow Breakdown</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 block">Total Value</span>
                            <span class="text-sm font-extrabold text-white mt-0.5 block" x-text="'$' + activeContract?.amount"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-center">
                            <span class="text-[10px] text-emerald-400 block">WorkForge Fee (10%)</span>
                            <span class="text-sm font-extrabold text-emerald-400 mt-0.5 block" x-text="'$' + activeContract?.platform_fee"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 block">In Escrow</span>
                            <span class="text-sm font-extrabold text-amber-400 mt-0.5 block" x-text="'$' + activeContract?.total_escrow"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-center">
                            <span class="text-[10px] text-slate-400 block">Released to Date</span>
                            <span class="text-sm font-extrabold text-emerald-400 mt-0.5 block" x-text="'$' + activeContract?.total_paid"></span>
                        </div>
                    </div>
                </div>

                <!-- Milestones Table -->
                <div class="space-y-2.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Milestones & Escrow Phases</span>
                    <div class="border border-slate-800 rounded-2xl overflow-hidden bg-slate-950">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-900/60 border-b border-slate-800 text-slate-400 text-[10px] font-bold uppercase">
                                    <th class="py-2.5 px-4">Milestone</th>
                                    <th class="py-2.5 px-3">Amount</th>
                                    <th class="py-2.5 px-3">Due Date</th>
                                    <th class="py-2.5 px-4 text-right">Escrow Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <template x-for="m in activeContract?.milestones" :key="m.id">
                                    <tr>
                                        <td class="py-2.5 px-4 font-semibold text-white" x-text="m.title"></td>
                                        <td class="py-2.5 px-3 font-bold text-emerald-400" x-text="'$' + m.amount"></td>
                                        <td class="py-2.5 px-3 text-slate-400" x-text="m.due_date"></td>
                                        <td class="py-2.5 px-4 text-right">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                  :class="{
                                                      'bg-emerald-500/20 text-emerald-400': m.status_raw === 'approved_and_released',
                                                      'bg-amber-500/20 text-amber-400': m.status_raw === 'funded_in_escrow' || m.status_raw === 'submitted_for_approval',
                                                      'bg-slate-800 text-slate-400': m.status_raw === 'created'
                                                  }"
                                                  x-text="m.status"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!activeContract?.milestones || activeContract.milestones.length === 0">
                                    <td colspan="4" class="py-4 text-center text-slate-500 text-xs">No individual milestones logged (Single lump sum escrow).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Terms & Scope -->
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Contract Scope & Terms</span>
                    <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line" x-text="activeContract?.terms"></p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between gap-3">
                <a :href="activeContract?.public_url" target="_blank" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 font-semibold">
                    <span>Open Live Contract Workroom</span> &rarr;
                </a>
                <button @click="closeContract()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    Close Dialog
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

