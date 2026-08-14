@extends('layouts.app')

@section('title', $freelancer->name . ' — ' . ($freelancer->freelancerProfile?->title ?? 'Specialist') . ' | WorkForge')
@section('meta_description', 'Hire ' . $freelancer->name . ' on WorkForge. Rate: $' . number_format($freelancer->freelancerProfile?->hourly_rate ?? 50, 2) . '/hr. ' . Str::limit(strip_tags($freelancer->freelancerProfile?->bio ?? ''), 120))
@section('og_title', $freelancer->name . ' — ' . ($freelancer->freelancerProfile?->title ?? 'Freelancer') . ' ($' . number_format($freelancer->freelancerProfile?->hourly_rate ?? 50, 0) . '/hr)')
@section('og_description', Str::limit(strip_tags($freelancer->freelancerProfile?->bio ?? ''), 160))
@section('og_image', $freelancer->avatar_url)
@section('og_type', 'profile')

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@type": "Person",
  "name": "{{ addslashes($freelancer->name) }}",
  "jobTitle": "{{ addslashes($freelancer->freelancerProfile?->title ?? 'Professional') }}",
  "image": "{{ $freelancer->avatar_url }}",
  "description": "{{ addslashes(strip_tags($freelancer->freelancerProfile?->bio ?? '')) }}",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "{{ addslashes($freelancer->city ?? 'Remote') }}",
    "addressCountry": "{{ addslashes($freelancer->country ?? 'Global') }}"
  }
}
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Profile Info -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <!-- Top Header -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                    <div class="flex items-start gap-5">
                        <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-2 border-slate-100 shadow-md">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $freelancer->name }}</h1>
                                @if($freelancer->freelancerProfile?->is_top_rated)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⭐ TOP RATED PLUS
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-emerald-700 mt-1">{{ $freelancer->freelancerProfile->title ?? 'Professional Specialist' }}</p>
                            <p class="text-xs text-slate-500 mt-1">📍 {{ $freelancer->city ? $freelancer->city . ', ' : '' }}{{ $freelancer->country ?? 'Worldwide' }}</p>

                            <!-- Social / External Links -->
                            <div class="flex items-center gap-3 pt-2 text-xs">
                                @if($freelancer->freelancerProfile?->github_url)
                                    <a href="{{ $freelancer->freelancerProfile->github_url }}" target="_blank" class="text-slate-500 hover:text-slate-900 font-semibold flex items-center gap-1">
                                        <span>GitHub &rarr;</span>
                                    </a>
                                @endif
                                @if($freelancer->freelancerProfile?->linkedin_url)
                                    <a href="{{ $freelancer->freelancerProfile->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                                        <span>LinkedIn &rarr;</span>
                                    </a>
                                @endif
                                @if($freelancer->freelancerProfile?->portfolio_url)
                                    <a href="{{ $freelancer->freelancerProfile->portfolio_url }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-semibold flex items-center gap-1">
                                        <span>Website &rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-left sm:text-right shrink-0">
                        <span class="text-3xl font-extrabold text-slate-900">${{ number_format($freelancer->freelancerProfile->hourly_rate ?? 30, 2) }}</span>
                        <span class="text-xs text-slate-400 block font-medium">/ hour</span>
                    </div>
                </div>

                <!-- Stats Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                    <div>
                        <span class="text-xs text-slate-400 block">Job Success</span>
                        <span class="text-lg font-extrabold text-emerald-700">{{ $freelancer->freelancerProfile->job_success_score ?? 100 }}%</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Total Earnings</span>
                        <span class="text-lg font-extrabold text-slate-900">${{ number_format($freelancer->freelancerProfile->total_earnings ?? 0) }}+</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Completed Jobs</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ $freelancer->freelancerProfile->completed_jobs_count ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Hours Worked</span>
                        <span class="text-lg font-extrabold text-slate-900">{{ number_format($freelancer->freelancerProfile->total_hours_worked ?? 0) }}</span>
                    </div>
                </div>

                <!-- Overview Bio -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Overview</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line prose prose-slate max-w-none">
                        {{ $freelancer->freelancerProfile->bio }}
                    </div>
                </div>

                <!-- Skills -->
                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Skills & Expertise</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($freelancer->skills as $skill)
                            <span class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-800 px-3 py-1.5 rounded-xl font-semibold transition">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Portfolio Gallery -->
                @if(!empty($freelancer->freelancerProfile->portfolio_items))
                    <div class="pt-6 border-t border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Portfolio Projects ({{ count($freelancer->freelancerProfile->portfolio_items) }})</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($freelancer->freelancerProfile->portfolio_items as $item)
                                <div class="group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs bg-slate-900 flex flex-col justify-between">
                                    <img src="{{ $item['image'] ?? 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=600' }}" alt="{{ $item['title'] }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300 opacity-90 group-hover:opacity-100">
                                    <div class="p-3 bg-white border-t border-slate-100">
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['title'] }}</h4>
                                        <span class="text-[11px] text-slate-400 block">{{ $item['category'] ?? 'Project' }}</span>
                                        @if(!empty($item['description']))
                                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-1">{{ $item['description'] }}</p>
                                        @endif
                                        @if(!empty($item['link']))
                                            <a href="{{ $item['link'] }}" target="_blank" class="text-[11px] text-emerald-600 font-bold hover:underline block mt-1">View Project &rarr;</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Employment History -->
                @if(!empty($freelancer->freelancerProfile->employment_history))
                    <div class="pt-6 border-t border-slate-100 space-y-3">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Employment History</h2>
                        <div class="space-y-3">
                            @foreach($freelancer->freelancerProfile->employment_history as $emp)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="flex items-center justify-between">
                                        <h3 class="font-bold text-sm text-slate-900">{{ $emp['title'] ?? 'Role' }} | <span class="text-slate-600 font-medium">{{ $emp['company'] ?? 'Company' }}</span></h3>
                                        <span class="text-xs text-slate-400 font-medium">{{ $emp['period'] ?? '' }}</span>
                                    </div>
                                    @if(!empty($emp['description']))
                                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">{{ $emp['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Education & Certifications -->
                @if(!empty($freelancer->freelancerProfile->education) || !empty($freelancer->freelancerProfile->certifications))
                    <div class="pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @if(!empty($freelancer->freelancerProfile->education))
                            <div class="space-y-3">
                                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Education</h2>
                                @foreach($freelancer->freelancerProfile->education as $edu)
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                        <h4 class="font-bold text-slate-900">{{ $edu['school'] ?? 'School' }}</h4>
                                        <p class="text-slate-600">{{ $edu['degree'] ?? '' }} • {{ $edu['year'] ?? '' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($freelancer->freelancerProfile->certifications))
                            <div class="space-y-3">
                                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Certifications</h2>
                                @foreach($freelancer->freelancerProfile->certifications as $cert)
                                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                        <h4 class="font-bold text-slate-900">🎖️ {{ $cert['name'] ?? 'Certificate' }}</h4>
                                        <p class="text-slate-600">{{ $cert['issuer'] ?? '' }} ({{ $cert['year'] ?? '' }})</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Work History & Client Reviews -->
                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Work History & Client Reviews ({{ $freelancer->reviewsReceived->count() }})</h2>

                    @forelse($freelancer->reviewsReceived as $review)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1 text-amber-500 font-bold text-sm">
                                    <span>⭐ {{ number_format($review->rating, 1) }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $review->created_at->format('M Y') }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $review->contract->title ?? 'Contract Review' }}</h4>
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                "{{ $review->feedback }}"
                            </p>
                            <div class="pt-2 flex items-center justify-between text-xs text-slate-400 border-t border-slate-200/60">
                                <span>Reviewed by: <strong>{{ $review->reviewer->name ?? 'Verified Client' }}</strong></span>
                                <span>Fixed Price: ${{ number_format($review->contract->amount ?? 0) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No client reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar / Contact Box -->
        <div class="lg:col-span-4 space-y-6">
            @if(Auth::check() && Auth::id() === $freelancer->id)
                <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-3xl space-y-3">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Your Public Profile</span>
                    <p class="text-xs text-emerald-700">This is how potential clients view your profile in marketplace searches.</p>
                    <a href="{{ route('profile.edit') }}" class="block text-center w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                        Edit Profile Sections
                    </a>
                </div>
            @endif

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Work with {{ $freelancer->name }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Have a project or contract ready? Message directly or invite to your open jobs.</p>

                @auth
                    @if(Auth::id() !== $freelancer->id)
                        <form action="{{ route('messages.start') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $freelancer->id }}">
                            <input type="hidden" name="subject" value="Project Inquiry with {{ $freelancer->name }}">
                            <textarea name="message" rows="3" required placeholder="Write a message to start a conversation..." class="w-full p-3 text-xs rounded-xl border border-slate-200 focus:ring-emerald-500"></textarea>
                            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                                Send Direct Message
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full block text-center py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                        Log In to Contact
                    </a>
                @endauth
            </div>

            <!-- Profile Badges & Verifications -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-3 text-xs">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider mb-2">Verifications & Hours</h3>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Identity:</span>
                    <span class="text-emerald-600 font-semibold">Verified ✓</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>English Proficiency:</span>
                    <span class="font-semibold text-slate-800 capitalize">{{ $freelancer->freelancerProfile->english_level ?? 'Fluent' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Availability:</span>
                    <span class="font-semibold text-emerald-600">{{ str_replace('_', ' ', ucfirst($freelancer->freelancerProfile->availability ?? 'available_now')) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
