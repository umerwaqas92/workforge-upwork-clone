@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-500 mb-6 gap-2">
        <a href="{{ route('jobs.show', $proposal->jobPosting->slug) }}" class="hover:text-emerald-600">Back to Job Post</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Proposal #{{ $proposal->id }}</span>
    </nav>

    <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xs space-y-8">
        <!-- Proposal Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <img src="{{ $proposal->freelancer->avatar_url }}" alt="{{ $proposal->freelancer->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-xs">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $proposal->freelancer->name }}</h1>
                    <p class="text-xs text-emerald-700 font-semibold">{{ $proposal->freelancer->freelancerProfile->title ?? 'Freelance Engineer' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">Submitted on {{ $proposal->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-2xl font-extrabold text-slate-900">${{ number_format($proposal->bid_amount, 2) }}</span>
                <span class="text-xs text-slate-400 block font-medium">Est. delivery: {{ $proposal->delivery_time_days ?? 14 }} days</span>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold {{ $proposal->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : ($proposal->status === 'shortlisted' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                    {{ ucfirst($proposal->status) }}
                </span>
            </div>
        </div>

        <!-- Job Target Brief -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 block">Job Applied For</span>
                <a href="{{ route('jobs.show', $proposal->jobPosting->slug) }}" class="text-sm font-bold text-slate-900 hover:text-emerald-600 transition">
                    {{ $proposal->jobPosting->title }}
                </a>
            </div>
            <span class="text-xs font-bold text-slate-700 bg-white px-3 py-1 rounded-xl border border-slate-200">
                Budget: {{ $proposal->jobPosting->budget_formatted }}
            </span>
        </div>

        <!-- Cover Letter -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Cover Letter</h2>
            <div class="p-6 rounded-2xl bg-slate-50/50 border border-slate-100 text-sm text-slate-700 leading-relaxed whitespace-pre-line prose max-w-none">
                {{ $proposal->cover_letter }}
            </div>
        </div>

        <!-- Proposed Milestones -->
        @if(!empty($proposal->milestones))
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Proposed Milestone Schedule</h2>
                <div class="space-y-2">
                    @foreach($proposal->milestones as $idx => $m)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <span class="font-medium text-slate-800">Milestone {{ $idx + 1 }}: {{ $m['title'] ?? 'Deliverable' }}</span>
                            <span class="font-bold text-emerald-700 text-sm">${{ number_format($m['amount'] ?? 0, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Client Management Controls -->
        @if(Auth::check() && Auth::id() === $proposal->jobPosting->client_id)
            <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <form action="{{ route('proposals.status', $proposal->id) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $proposal->status === 'shortlisted' ? 'pending' : 'shortlisted' }}">
                    <button type="submit" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                        {{ $proposal->status === 'shortlisted' ? 'Remove from Shortlist' : '⭐ Shortlist Candidate' }}
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <form action="{{ route('messages.start') }}" method="POST">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $proposal->freelancer_id }}">
                        <input type="hidden" name="job_id" value="{{ $proposal->job_posting_id }}">
                        <input type="hidden" name="subject" value="Regarding proposal for: {{ $proposal->jobPosting->title }}">
                        <input type="hidden" name="message" value="Hi {{ $proposal->freelancer->name }}, I reviewed your proposal and would like to discuss details.">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            Message Candidate
                        </button>
                    </form>

                    @if($proposal->status !== 'accepted')
                        <a href="{{ route('contracts.hire', $proposal->id) }}" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow transition">
                            Hire & Set Up Contract
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
