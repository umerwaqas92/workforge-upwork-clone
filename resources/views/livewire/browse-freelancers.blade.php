<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm">Filter Talent</h3>
                <button wire:click="resetFilters" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold">Reset all</button>
            </div>

            <!-- Top Rated Checkbox -->
            <div>
                <label class="flex items-center text-sm text-slate-700 font-medium cursor-pointer">
                    <input type="checkbox" wire:model.live="onlyTopRated" class="text-emerald-600 focus:ring-emerald-500 rounded mr-2">
                    <span>⭐ Top Rated Talent Only</span>
                </label>
            </div>

            <!-- Experience Level -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Experience</label>
                <select wire:model.live="experienceLevel" class="w-full text-xs rounded-xl border-slate-200 text-slate-700 focus:ring-emerald-500 focus:border-emerald-500 py-2">
                    <option value="">Any Experience Level</option>
                    <option value="entry">Entry Level</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="expert">Expert</option>
                </select>
            </div>

            <!-- Hourly Rate Range -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hourly Rate ($/hr)</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" wire:model.live.debounce.300ms="minRate" placeholder="Min $" class="text-xs rounded-xl border-slate-200 py-2 px-3">
                    <input type="number" wire:model.live.debounce.300ms="maxRate" placeholder="Max $" class="text-xs rounded-xl border-slate-200 py-2 px-3">
                </div>
            </div>
        </div>
    </div>

    <!-- Freelancers Stream -->
    <div class="lg:col-span-3 space-y-4">
        <!-- Search Header -->
        <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="relative w-full">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, title, skills (e.g. 'Laravel', 'Figma', 'Python')..." class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 text-slate-900 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>

        <!-- Freelancer Cards -->
        <div class="space-y-4">
            @forelse($freelancers as $freelancer)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 hover:shadow-md transition duration-200">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-xs shrink-0">
                            <div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('freelancers.show', $freelancer->id) }}" class="text-lg font-bold text-slate-900 hover:text-emerald-600 transition">
                                        {{ $freelancer->name }}
                                    </a>
                                    @if($freelancer->freelancerProfile?->is_top_rated)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            TOP RATED
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs font-semibold text-emerald-700 mt-0.5">{{ $freelancer->freelancerProfile->title ?? 'Specialist' }}</p>
                                <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                    <span>📍 {{ $freelancer->country ?? 'Worldwide' }}</span>
                                    <span>⭐ {{ $freelancer->rating }} ({{ $freelancer->rating_count }} reviews)</span>
                                    <span>{{ $freelancer->freelancerProfile->job_success_score ?? 100 }}% Job Success</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-left sm:text-right shrink-0">
                            <span class="text-xl font-extrabold text-slate-900">${{ number_format($freelancer->freelancerProfile->hourly_rate ?? 30, 2) }}</span>
                            <span class="text-xs text-slate-500 block">/ hour</span>
                            <a href="{{ route('freelancers.show', $freelancer->id) }}" class="mt-2 inline-block px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                                View Profile
                            </a>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 line-clamp-3 mt-4 leading-relaxed">
                        {{ $freelancer->freelancerProfile->bio }}
                    </p>

                    <!-- Skills Chips -->
                    <div class="flex flex-wrap gap-1.5 mt-4 pt-3 border-t border-slate-100">
                        @foreach($freelancer->skills as $skill)
                            <span class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md font-medium">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
                    <h3 class="text-base font-bold text-slate-800">No freelancers found</h3>
                    <p class="text-xs text-slate-500 mt-1">Try broadening your search term or adjusting filters.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $freelancers->links() }}
        </div>
    </div>
</div>
