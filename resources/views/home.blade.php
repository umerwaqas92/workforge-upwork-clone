@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="relative overflow-x-clip bg-gradient-to-b from-emerald-950 via-slate-900 to-slate-900 text-white min-h-[580px] sm:min-h-[660px] lg:min-h-[720px] pt-16 sm:pt-24 lg:pt-32 pb-20 sm:pb-28 lg:pb-36 flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                <!-- Verified Freelancers Live Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-semibold max-w-full">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <span class="truncate sm:whitespace-normal">{{ $stats['total_freelancers'] ?? 10 }}+ Verified Independent Professionals Available Now</span>
                </div>
                
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight sm:leading-tight pt-1 pb-1">
                    How work <br class="hidden sm:block">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">should work</span>.
                </h1>
                
                <p class="text-base sm:text-lg text-slate-300 max-w-xl leading-relaxed">
                    Forget the old rules. Hire the top 1% of independent talent or discover high-paying projects with protected milestone escrow payments.
                </p>

                <!-- Search Bar with Increased Height -->
                <form action="{{ route('jobs.index') }}" method="GET" class="bg-white p-2 sm:p-2.5 rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col sm:flex-row gap-2 max-w-xl border border-white/20">
                    <div class="flex-1 flex items-center gap-3 px-4 py-3 sm:py-3.5">
                        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" placeholder="Try 'Laravel developer', 'UI/UX Designer', 'Python'..." class="w-full text-slate-900 placeholder-slate-400 text-sm sm:text-base focus:outline-none border-none ring-0">
                    </div>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm sm:text-base font-bold px-7 py-3.5 sm:py-3.5 rounded-xl sm:rounded-2xl transition shadow-md shrink-0 flex items-center justify-center gap-2">
                        <span>Find Work</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Popular Tags -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs text-slate-400">
                    <span class="font-medium text-slate-300">Popular:</span>
                    <a href="{{ route('jobs.index', ['search' => 'Laravel']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 transition font-medium">Laravel</a>
                    <a href="{{ route('jobs.index', ['search' => 'Vue.js']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 transition font-medium">Vue.js</a>
                    <a href="{{ route('jobs.index', ['search' => 'Figma']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 transition font-medium">Figma UI/UX</a>
                    <a href="{{ route('jobs.index', ['search' => 'AI']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 transition font-medium">AI & LLMs</a>
                    <a href="{{ route('jobs.index', ['search' => 'Flutter']) }}" class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 transition font-medium">Flutter</a>
                </div>
            </div>

            <!-- Hero Dynamic Top Talent Card from DB -->
            @php $topHero = $topFreelancers->first(); @endphp
            <div class="lg:col-span-5 relative hidden lg:block">
                <div class="relative w-full max-w-md mx-auto space-y-4">
                    @if($topHero)
                        <div class="bg-slate-800/90 backdrop-blur-md p-6 rounded-3xl border border-slate-700/60 shadow-xl text-white transform hover:-translate-y-1 transition duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    <svg class="w-3 h-3 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span>TOP RATED PLUS</span>
                                </span>
                                <span class="text-sm font-bold text-emerald-400">${{ number_format($topHero->freelancerProfile->hourly_rate ?? 75, 2) }}/hr</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <img src="{{ $topHero->avatar_url }}" alt="{{ $topHero->name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-600">
                                <div>
                                    <a href="{{ route('freelancers.show', $topHero->id) }}" class="font-bold text-white text-base hover:text-emerald-400 transition">{{ $topHero->name }}</a>
                                    <p class="text-xs text-slate-400 line-clamp-1">{{ $topHero->freelancerProfile->title ?? 'Senior Engineer' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-700/60 flex items-center justify-between text-xs text-slate-300">
                                <span>{{ $topHero->freelancerProfile->job_success_score ?? 100 }}% Job Success</span>
                                <span>{{ $topHero->freelancerProfile->completed_jobs_count ?? 38 }} Completed Jobs</span>
                            </div>
                        </div>
                    @endif

                    <!-- Escrow Protection Badge Card -->
                    <div class="bg-emerald-950/70 backdrop-blur-md p-5 rounded-3xl border border-emerald-600/30 shadow-xl text-emerald-100 flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-600 flex items-center justify-center text-white shrink-0 shadow-md">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-white text-sm">Guaranteed Escrow Protection</p>
                            <p class="text-emerald-300/80 mt-0.5">Funds stay secure until you review and approve the submitted milestones.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Browse Talent by Category -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Browse talent by category</h2>
            <p class="text-sm text-slate-500 mt-1">Explore top specialists across high-demand disciplines.</p>
        </div>
        <a href="{{ route('freelancers.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
            Browse all talent &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('jobs.index', ['selectedCategory' => $category->id]) }}" class="group bg-white p-6 rounded-2xl border border-slate-200/80 hover:border-emerald-500/50 hover:shadow-lg transition duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-xs">
                    @if(str_contains($category->slug, 'dev') || str_contains($category->slug, 'it'))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    @elseif(str_contains($category->slug, 'ai'))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    @elseif(str_contains($category->slug, 'design') || str_contains($category->slug, 'creative'))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4 5 5 0 013-4.5V11a7 7 0 0113.33-3.07 7 7 0 011.67 4.57 5 5 0 01-3 4.5V17a4 4 0 01-4 4H7z"/></svg>
                    @elseif(str_contains($category->slug, 'mobile'))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @elseif(str_contains($category->slug, 'market') || str_contains($category->slug, 'sales'))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    @endif
                </div>
                <h3 class="font-bold text-slate-900 text-base group-hover:text-emerald-600 transition">{{ $category->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $category->description }}</p>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                    <span class="font-medium">{{ $category->jobs_count ?? 0 }} open jobs</span>
                    <span class="text-emerald-600 font-bold group-hover:translate-x-1 transition">&rarr;</span>
                </div>
            </a>
        @endforeach
    </div>
</section>

<!-- Featured Job Opportunities -->
<section class="bg-slate-100/60 py-16 border-y border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Fresh Opportunities</span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Featured Job Openings</h2>
            </div>
            <a href="{{ route('jobs.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                View all jobs &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($featuredJobs as $job)
                <div class="bg-white p-6 rounded-2xl border transition duration-200 flex flex-col justify-between {{ $job->is_featured ? 'bg-gradient-to-r from-amber-500/[0.03] via-white to-amber-500/[0.03] border-amber-300 shadow-md shadow-amber-500/10 ring-1 ring-amber-400/40 hover:border-amber-400' : 'border-slate-200/80 hover:border-emerald-500/40 hover:shadow-md' }}">
                    <div>
                        <div class="flex items-center justify-between gap-4 text-xs text-slate-500 mb-2">
                            <div class="flex items-center gap-2">
                                @if($job->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-xs">
                                        <span>✨</span> FEATURED
                                    </span>
                                @endif
                                <span>Posted {{ $job->published_at ? $job->published_at->diffForHumans() : 'recently' }}</span>
                            </div>
                            <span class="font-bold {{ $job->is_featured ? 'text-amber-900 bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50' }} px-2.5 py-0.5 rounded-full">
                                {{ $job->budget_formatted }}
                            </span>
                        </div>
                        <a href="{{ route('jobs.show', $job->slug) }}" class="block text-lg font-bold text-slate-900 hover:text-emerald-600 transition mb-2">
                            {{ $job->title }}
                        </a>
                        <p class="text-sm text-slate-600 line-clamp-3 mb-4 leading-relaxed">
                            {{ $job->description }}
                        </p>
                        
                        <!-- Skills -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach($job->skills->take(4) as $skill)
                                <span class="text-xs bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md font-medium">
                                    {{ $skill->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-slate-800">{{ $job->client->name }}</span>
                            @if($job->client->clientProfile?->payment_verified)
                                <span class="text-emerald-600 flex items-center gap-0.5 font-semibold" title="Payment Verified">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Verified
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('jobs.show', $job->slug) }}" class="text-emerald-600 font-semibold hover:underline">
                            View details &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Top Freelancer Highlights -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Top-Tier Talent</span>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Highest-Rated Freelancers</h2>
        </div>
        <a href="{{ route('freelancers.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
            Browse all freelancers &rarr;
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($topFreelancers as $freelancer)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 hover:shadow-md transition duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shadow-xs">
                        <div>
                            <a href="{{ route('freelancers.show', $freelancer->id) }}" class="font-bold text-slate-900 text-base leading-tight hover:text-emerald-600 transition">{{ $freelancer->name }}</a>
                            <p class="text-xs text-slate-500">{{ $freelancer->country }}</p>
                            <span class="text-xs font-bold text-emerald-600">${{ number_format($freelancer->freelancerProfile->hourly_rate ?? 30, 2) }}/hr</span>
                        </div>
                    </div>
                    
                    <p class="text-xs font-semibold text-slate-800 line-clamp-1 mb-2">{{ $freelancer->freelancerProfile->title ?? 'Professional Specialist' }}</p>
                    <p class="text-xs text-slate-500 line-clamp-3 mb-4 leading-relaxed">{{ $freelancer->freelancerProfile->bio }}</p>

                    <!-- Skills -->
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($freelancer->skills->take(3) as $skill)
                            <span class="text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <span>{{ number_format($freelancer->rating, 1) }}</span>
                        <span class="text-slate-400 text-[11px] font-normal">({{ $freelancer->rating_count }})</span>
                    </span>
                    <a href="{{ route('freelancers.show', $freelancer->id) }}" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg font-medium transition">
                        Profile
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Call to Action Banner -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-8 sm:p-12 text-white shadow-xl flex flex-col lg:flex-row items-center justify-between gap-8">
        <div class="space-y-3 max-w-xl">
            <h3 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Ready to start hiring or freelancing?</h3>
            <p class="text-emerald-100 text-sm leading-relaxed">Join thousands of businesses and independent experts getting high-impact work done every day.</p>
        </div>
        <div class="flex flex-wrap gap-4 shrink-0">
            <a href="{{ route('jobs.create') }}" class="bg-white text-emerald-900 hover:bg-emerald-50 px-6 py-3 rounded-full font-bold text-sm shadow transition">
                Post a Project
            </a>
            <a href="{{ route('jobs.index') }}" class="bg-emerald-900/60 hover:bg-emerald-900 text-white px-6 py-3 rounded-full font-bold text-sm border border-emerald-400/40 transition">
                Find Great Work
            </a>
        </div>
    </div>
</section>
@endsection
