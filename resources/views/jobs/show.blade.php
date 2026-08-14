@extends('layouts.app')

@section('title', $job->title . ' | Freelance Job on WorkForge')
@section('meta_description', Str::limit(strip_tags($job->description), 150) . ' Budget: $' . number_format($job->budget_min) . ' - $' . number_format($job->budget_max) . '. Skills: ' . $job->skills->pluck('name')->join(', '))
@section('og_title', $job->title . ' — $' . number_format($job->budget_min) . '-' . number_format($job->budget_max) . ' (' . ucfirst($job->budget_type) . ')')
@section('og_description', Str::limit(strip_tags($job->description), 160))
@section('og_type', 'article')

@section('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@type": "JobPosting",
  "title": "{{ addslashes($job->title) }}",
  "description": "{{ addslashes(strip_tags($job->description)) }}",
  "identifier": {
    "@type": "PropertyValue",
    "name": "WorkForge",
    "value": "{{ $job->id }}"
  },
  "datePosted": "{{ $job->created_at->toIso8601String() }}",
  "employmentType": "CONTRACTOR",
  "hiringOrganization": {
    "@type": "Organization",
    "name": "{{ addslashes($job->client->name ?? 'Verified Client') }}"
  },
  "jobLocationType": "TELECOMMUTE",
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "USD",
    "value": {
      "@type": "QuantitativeValue",
      "minValue": {{ $job->budget_min }},
      "maxValue": {{ $job->budget_max }},
      "unitText": "{{ $job->budget_type === 'hourly' ? 'HOUR' : 'PROJECT' }}"
    }
  }
}
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-slate-500 mb-6 gap-2">
        <a href="{{ route('jobs.index') }}" class="hover:text-emerald-600">Jobs</a>
        <span>/</span>
        <a href="{{ route('jobs.index', ['selectedCategory' => $job->category_id]) }}" class="hover:text-emerald-600">{{ $job->category->name ?? 'General' }}</a>
        <span>/</span>
        <span class="text-slate-800 font-medium truncate max-w-xs">{{ $job->title }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Job Details -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <!-- Header -->
                <div class="space-y-3 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        @if($job->is_featured)
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                🌟 Featured
                            </span>
                        @endif
                        @if($job->is_urgent)
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                ⚡ Urgent
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">Posted {{ $job->published_at ? $job->published_at->diffForHumans() : 'recently' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">{{ $job->title }}</h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 pt-2">
                        <span class="font-medium">Category: <strong class="text-slate-900">{{ $job->category->name ?? 'N/A' }}</strong></span>
                        <span>•</span>
                        <span>Experience Level: <strong class="text-slate-900">{{ $job->formatted_experience }}</strong></span>
                        <span>•</span>
                        <span>Duration: <strong class="text-slate-900">{{ str_replace('_', ' ', $job->duration) }}</strong></span>
                    </div>
                </div>

                <!-- Rate / Budget Banner -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            $
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 font-medium block">Planned Compensation ({{ ucfirst($job->type) }})</span>
                            <span class="text-xl font-extrabold text-emerald-700">{{ $job->budget_formatted }}</span>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <span class="block font-semibold text-slate-700">Project Type</span>
                        <span>{{ $job->type === 'fixed_price' ? 'Milestone Escrow' : 'Hourly Tracked' }}</span>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Project Description</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line prose prose-slate max-w-none">
                        {{ $job->description }}
                    </div>
                </div>

                <!-- Skills & Deliverables -->
                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Skills & Expertise Required</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills as $skill)
                            <span class="text-xs bg-emerald-50 text-emerald-800 border border-emerald-200/60 px-3 py-1.5 rounded-xl font-semibold">
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Activity on this job -->
                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Activity on this Job</h2>
                    <div class="grid grid-cols-3 gap-4 text-xs">
                        <div class="bg-slate-50 p-3 rounded-xl">
                            <span class="text-slate-400 block">Proposals:</span>
                            <span class="text-base font-bold text-slate-800">{{ $job->proposals_count }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl">
                            <span class="text-slate-400 block">Interviewing:</span>
                            <span class="text-base font-bold text-slate-800">{{ $job->proposals()->where('status', 'shortlisted')->count() }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl">
                            <span class="text-slate-400 block">Hires:</span>
                            <span class="text-base font-bold text-slate-800">{{ $job->hires_count }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proposals Section (Visible to Client Owner) -->
            @if(Auth::check() && (Auth::id() === $job->client_id || Auth::user()->isAdmin()))
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Received Proposals ({{ $job->proposals->count() }})</h2>
                            <p class="text-xs text-slate-500">Review candidates, message them, and hire directly with funded escrow.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($job->proposals as $proposal)
                            <div class="p-5 rounded-2xl border border-slate-200 hover:border-emerald-500/50 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $proposal->freelancer->avatar_url }}" alt="{{ $proposal->freelancer->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200">
                                        <div>
                                            <a href="{{ route('freelancers.show', $proposal->freelancer_id) }}" class="font-bold text-slate-900 hover:text-emerald-600 text-base">
                                                {{ $proposal->freelancer->name }}
                                            </a>
                                            <p class="text-xs text-slate-500">{{ $proposal->freelancer->freelancerProfile->title ?? 'Freelancer' }} • {{ $proposal->freelancer->country }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-extrabold text-emerald-700">${{ number_format($proposal->bid_amount, 2) }}</span>
                                        <span class="block text-[11px] text-slate-400">in {{ $proposal->delivery_time_days ?? 14 }} days</span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 line-clamp-3 mb-4 bg-slate-50 p-3 rounded-xl leading-relaxed">
                                    "{{ $proposal->cover_letter }}"
                                </p>

                                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100 text-xs">
                                    <span class="inline-flex items-center gap-1 font-semibold px-2 py-0.5 rounded-full {{ $proposal->status === 'shortlisted' ? 'bg-amber-50 text-amber-700' : ($proposal->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                        Status: {{ ucfirst($proposal->status) }}
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('proposals.show', $proposal->id) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold transition">
                                            View Full Proposal
                                        </a>
                                        @if($proposal->status !== 'accepted')
                                            <a href="{{ route('contracts.hire', $proposal->id) }}" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-xs">
                                                Hire Freelancer
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-6">No proposals received yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Actions & Client Info -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Apply Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                @guest
                    <a href="{{ route('login') }}" class="w-full block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3 px-4 rounded-2xl shadow transition">
                        Log In to Apply
                    </a>
                @else
                    @if(Auth::user()->isFreelancer())
                        @if($existingProposal)
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center space-y-2">
                                <span class="text-xs font-bold uppercase tracking-wider block">Proposal Submitted</span>
                                <p class="text-xs">You bid ${{ number_format($existingProposal->bid_amount, 2) }} on {{ $existingProposal->created_at->format('M d, Y') }}</p>
                                <a href="{{ route('proposals.show', $existingProposal->id) }}" class="inline-block text-xs font-bold text-emerald-700 underline">View Your Proposal &rarr;</a>
                            </div>
                        @else
                            <a href="{{ route('proposals.create', $job->slug) }}" class="w-full block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-3.5 px-4 rounded-2xl shadow-md transition">
                                Apply Now (Submit Proposal)
                            </a>
                        @endif

                        <form action="{{ route('jobs.save', $job->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                                <svg class="w-4 h-4 {{ $isSaved ? 'text-red-500 fill-red-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                {{ $isSaved ? 'Saved in My Jobs' : 'Save Job' }}
                            </button>
                        </form>
                    @endif
                @endguest
            </div>

            <!-- Client Information -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">About the Client</h3>

                <div class="flex items-center gap-3">
                    <img src="{{ $job->client->avatar_url }}" alt="{{ $job->client->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $job->client->clientProfile?->company_name ?? $job->client->name }}</h4>
                        <p class="text-xs text-slate-500">Member since {{ $job->client->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="space-y-3 pt-3 border-t border-slate-100 text-xs text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Payment Status:</span>
                        <span class="font-semibold text-emerald-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Payment Verified
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Location:</span>
                        <span class="font-medium text-slate-800">{{ $job->client->country ?? 'Worldwide' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Spent:</span>
                        <span class="font-bold text-slate-900">${{ number_format($job->client->clientProfile?->total_spent ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Total Hires:</span>
                        <span class="font-medium text-slate-800">{{ $job->client->clientProfile?->hires_count ?? 0 }} hires</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Client Rating:</span>
                        <span class="font-bold text-amber-600">⭐ {{ $job->client->rating }} ({{ $job->client->rating_count }} reviews)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
