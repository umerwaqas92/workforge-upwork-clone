<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Filter Jobs</h3>
                <button wire:click="resetFilters" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Reset all</button>
            </div>

            <!-- Job Type -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Job Type</label>
                <div class="space-y-2">
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="radio" wire:model.live="jobType" value="" class="text-emerald-600 focus:ring-emerald-500 rounded-full">
                        <span class="ml-2">All Job Types</span>
                    </label>
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="radio" wire:model.live="jobType" value="fixed_price" class="text-emerald-600 focus:ring-emerald-500 rounded-full">
                        <span class="ml-2">Fixed-Price</span>
                    </label>
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="radio" wire:model.live="jobType" value="hourly" class="text-emerald-600 focus:ring-emerald-500 rounded-full">
                        <span class="ml-2">Hourly Rate</span>
                    </label>
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category</label>
                <select wire:model.live="selectedCategory" class="w-full text-xs rounded-xl border-slate-200 text-slate-700 focus:ring-emerald-500 focus:border-emerald-500 py-2">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Experience Level -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Experience Level</label>
                <div class="space-y-2">
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="checkbox" wire:model.live="selectedExperience" value="entry" class="text-emerald-600 focus:ring-emerald-500 rounded">
                        <span class="ml-2">Entry Level ($)</span>
                    </label>
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="checkbox" wire:model.live="selectedExperience" value="intermediate" class="text-emerald-600 focus:ring-emerald-500 rounded">
                        <span class="ml-2">Intermediate ($$)</span>
                    </label>
                    <label class="flex items-center text-sm text-slate-600 hover:text-slate-900 cursor-pointer">
                        <input type="checkbox" wire:model.live="selectedExperience" value="expert" class="text-emerald-600 focus:ring-emerald-500 rounded">
                        <span class="ml-2">Expert ($$$)</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Listings Stream -->
    <div class="lg:col-span-3 space-y-4">
        <!-- Search & Sorting Header -->
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:max-w-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by title, keywords, or skills..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-auto text-xs text-slate-500">
                <span class="font-medium">Sort by:</span>
                <select wire:model.live="sortBy" class="text-xs rounded-xl border-slate-200 text-slate-700 py-1.5 focus:ring-emerald-500">
                    <option value="latest">Newest First</option>
                    <option value="budget_high">Highest Budget</option>
                    <option value="proposals_low">Fewest Proposals</option>
                </select>
            </div>
        </div>

        <!-- Job Cards -->
        <div class="space-y-3">
            @forelse($jobs as $job)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 hover:shadow-md transition duration-200">
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <div>
                            <span class="text-xs text-slate-400">Posted {{ $job->published_at ? $job->published_at->diffForHumans() : 'recently' }}</span>
                            <a href="{{ route('jobs.show', $job->slug) }}" class="block text-lg font-bold text-slate-900 hover:text-emerald-600 transition mt-0.5">
                                {{ $job->title }}
                            </a>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                {{ $job->budget_formatted }}
                            </span>
                            <span class="block text-[11px] text-slate-400 mt-1 capitalize">{{ str_replace('_', ' ', $job->type) }}</span>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 line-clamp-3 mb-4 leading-relaxed">
                        {{ $job->description }}
                    </p>

                    <!-- Skills Chips -->
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($job->skills as $skill)
                            <span class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md font-medium">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Client & Meta Info -->
                    <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1 text-slate-700 font-medium">
                                @if($job->client->clientProfile?->payment_verified)
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                                <span>{{ $job->client->clientProfile?->company_name ?? $job->client->name }}</span>
                            </div>
                            <span>📍 {{ $job->client->country ?? 'Worldwide' }}</span>
                            <span>⭐ {{ $job->client->rating }} ({{ $job->client->rating_count }} reviews)</span>
                            <span>Proposals: <strong>{{ $job->proposals_count }}</strong></span>
                        </div>

                        <a href="{{ route('jobs.show', $job->slug) }}" class="text-emerald-600 font-bold hover:text-emerald-700 flex items-center gap-1">
                            View Job & Apply &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-base font-bold text-slate-800">No jobs match your search criteria</h3>
                    <p class="text-xs text-slate-500 mt-1">Try adjusting your filters, keyword search, or resetting all filters.</p>
                    <button wire:click="resetFilters" class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">Reset Filters</button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
