@extends('layouts.admin')

@section('content')
<div x-data="{
    activeJob: null,
    showModal: false,
    openJob(job) {
        this.activeJob = job;
        this.showModal = true;
    },
    closeJob() {
        this.showModal = false;
        this.activeJob = null;
    }
}" class="space-y-6">

    <!-- Header & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Job Moderation & Postings</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Review live marketplace listings, budgets, scopes, and proposal activity.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300">
                Total: <span class="text-emerald-400">{{ $jobs->total() }}</span> Postings
            </span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.jobs') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Search Project</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or keyword..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 pr-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition">
                    @if(request('search'))
                        <a href="{{ route('admin.jobs', request()->except('search')) }}" class="absolute inset-y-0 right-2 flex items-center text-slate-500 hover:text-white transition" title="Clear search">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Payment Type</label>
                <select name="type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 transition">
                    <option value="">All Types</option>
                    <option value="fixed_price" {{ request('type') === 'fixed_price' ? 'selected' : '' }}>Fixed Price</option>
                    <option value="hourly" {{ request('type') === 'hourly' ? 'selected' : '' }}>Hourly Rate</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-950/40 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'type']))
                    <a href="{{ route('admin.jobs') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 px-3 rounded-xl text-xs transition" title="Clear Filters">✕</a>
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
                        <th class="py-3.5 px-5">Job Details</th>
                        <th class="py-3.5 px-4">Client</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Budget / Rate</th>
                        <th class="py-3.5 px-4">Proposals</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($jobs as $j)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-5 max-w-xs">
                                <span class="font-bold text-white block truncate text-xs sm:text-sm">{{ $j->title }}</span>
                                <span class="text-slate-500 text-[11px] block mt-0.5">{{ ucfirst(str_replace('_', ' ', $j->type)) }} • {{ $j->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $j->client->avatar_url }}" alt="{{ $j->client->name }}" class="w-6 h-6 rounded-lg object-cover">
                                    <span class="text-slate-300 font-medium">{{ $j->client->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-[11px] text-slate-300">
                                    {{ $j->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-emerald-400 whitespace-nowrap">
                                {{ $j->budget_formatted }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-bold text-[11px]">
                                    {{ $j->proposals_count ?? $j->proposals->count() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $j->status === 'open' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : ($j->status === 'in_progress' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : ($j->status === 'completed' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700')) }}">
                                    {{ str_replace('_', ' ', $j->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <button type="button"
                                        @click="openJob({{ Js::from([
                                            'id' => $j->id,
                                            'title' => $j->title,
                                            'slug' => $j->slug,
                                            'description' => $j->description,
                                            'type' => ucfirst(str_replace('_', ' ', $j->type)),
                                            'budget_formatted' => $j->budget_formatted,
                                            'experience_level' => $j->formatted_experience,
                                            'duration' => ucfirst(str_replace('_', ' ', $j->duration ?? 'not_specified')),
                                            'weekly_hours' => ucfirst(str_replace('_', ' ', $j->weekly_hours ?? 'none')),
                                            'status' => $j->status,
                                            'status_formatted' => ucfirst(str_replace('_', ' ', $j->status)),
                                            'category' => $j->category->name ?? 'General',
                                            'skills' => $j->skills->pluck('name'),
                                            'proposals_count' => $j->proposals_count ?? $j->proposals->count(),
                                            'hires_count' => $j->hires_count ?? 0,
                                            'created_at_formatted' => $j->created_at->format('M d, Y (h:i A)'),
                                            'client' => [
                                                'name' => $j->client->name,
                                                'email' => $j->client->email,
                                                'avatar_url' => $j->client->avatar_url,
                                                'country' => $j->client->country ?? 'Worldwide',
                                                'company' => $j->client->clientProfile->company_name ?? 'Individual Client',
                                            ],
                                            'public_url' => route('jobs.show', $j->slug),
                                            'status_route' => route('admin.jobs.status', $j->id),
                                        ]) }})"
                                        class="px-3 py-1.5 rounded-xl bg-slate-850 hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold border border-slate-750 hover:border-emerald-500 text-xs transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Inspect Job</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="space-y-2">
                                    <span class="text-3xl block">💼</span>
                                    <span class="text-sm font-semibold block">No job postings found matching your query.</span>
                                    <a href="{{ route('admin.jobs') }}" class="text-xs text-emerald-400 hover:underline">Reset search filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>

    <!-- Job Details Dialog Modal -->
    <div x-show="showModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
         @keydown.escape.window="closeJob()">

        <div @click.outside="closeJob()" 
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">

            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-slate-800 bg-slate-950/70 flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="activeJob?.category"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="activeJob?.type"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                              :class="{
                                  'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': activeJob?.status === 'open',
                                  'bg-blue-500/20 text-blue-400 border border-blue-500/30': activeJob?.status === 'in_progress',
                                  'bg-purple-500/20 text-purple-400 border border-purple-500/30': activeJob?.status === 'completed',
                                  'bg-slate-800 text-slate-400 border border-slate-700': activeJob?.status === 'closed' || activeJob?.status === 'draft'
                              }"
                              x-text="activeJob?.status_formatted"></span>
                    </div>
                    <h3 class="text-base sm:text-xl font-extrabold text-white leading-tight" x-text="activeJob?.title"></h3>
                </div>

                <button @click="closeJob()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-6 text-xs text-slate-300">
                
                <!-- Client Card & Budget Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <img :src="activeJob?.client.avatar_url" :alt="activeJob?.client.name" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-700 shrink-0">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Posted By Client</span>
                            <span class="text-xs sm:text-sm font-bold text-white block truncate" x-text="activeJob?.client.name"></span>
                            <span class="text-[11px] text-slate-400 truncate block" x-text="activeJob?.client.company + ' • ' + activeJob?.client.country"></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400 block">Budget / Compensation</span>
                        <span class="text-lg font-extrabold text-emerald-400 block mt-0.5" x-text="activeJob?.budget_formatted"></span>
                        <span class="text-[11px] text-slate-400 block" x-text="activeJob?.experience_level"></span>
                    </div>
                </div>

                <!-- Scope Details Grid -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-center">
                        <span class="text-[10px] text-slate-400 block">Duration</span>
                        <span class="text-xs font-bold text-white mt-0.5 block truncate" x-text="activeJob?.duration"></span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-center">
                        <span class="text-[10px] text-slate-400 block">Proposals Received</span>
                        <span class="text-xs font-bold text-emerald-400 mt-0.5 block" x-text="activeJob?.proposals_count"></span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-center">
                        <span class="text-[10px] text-slate-400 block">Posted Date</span>
                        <span class="text-xs font-bold text-white mt-0.5 block truncate" x-text="activeJob?.created_at_formatted"></span>
                    </div>
                </div>

                <!-- Job Full Description -->
                <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Full Job Description</span>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed whitespace-pre-line" x-text="activeJob?.description"></p>
                </div>

                <!-- Required Skills -->
                <div x-show="activeJob?.skills?.length > 0">
                    <span class="text-[10px] font-bold uppercase text-slate-400 block mb-2">Required Skills & Technologies</span>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="skill in activeJob?.skills" :key="skill">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-200" x-text="skill"></span>
                        </template>
                    </div>
                </div>

                <!-- Job Moderation Form -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-300">Moderation & Visibility Status</h4>
                    <form :action="activeJob?.status_route" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        @method('PATCH')
                        <div class="flex-1">
                            <select name="status" class="w-full bg-slate-900 border border-slate-750 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                                <option value="open" :selected="activeJob?.status === 'open'">Open (Accepting proposals)</option>
                                <option value="in_progress" :selected="activeJob?.status === 'in_progress'">In Progress (Contract active)</option>
                                <option value="completed" :selected="activeJob?.status === 'completed'">Completed</option>
                                <option value="closed" :selected="activeJob?.status === 'closed'">Closed</option>
                                <option value="draft" :selected="activeJob?.status === 'draft'">Draft (Hidden from marketplace)</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-md">
                            Save Status Change
                        </button>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between gap-3">
                <a :href="activeJob?.public_url" target="_blank" class="text-xs text-emerald-400 hover:underline flex items-center gap-1 font-semibold">
                    <span>View Public Job Page</span> &rarr;
                </a>
                <button @click="closeJob()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                    Close Dialog
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

