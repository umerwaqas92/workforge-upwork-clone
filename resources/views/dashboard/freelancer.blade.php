@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Top Greeting Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Welcome back, {{ $user->name }} 👋</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $user->freelancerProfile->title ?? 'Freelance Specialist' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('freelancers.show', $user->id) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                View Public Profile
            </a>
            <a href="{{ route('jobs.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                Search Fresh Jobs
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block mb-1">Total Earnings</span>
            <span class="text-2xl font-extrabold text-emerald-700">${{ number_format($metrics['total_earnings'], 2) }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Job Success Score</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $metrics['job_success_score'] }}%</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Active Contracts</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $metrics['active_contracts'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Proposals Submitted</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $metrics['proposals_submitted'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Column: Active Contracts & Recommended Jobs -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Active Contracts -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Active Contracts ({{ $activeContracts->count() }})</h2>

                <div class="space-y-3">
                    @forelse($activeContracts as $contract)
                        <div class="p-4 rounded-2xl border border-slate-200 hover:border-emerald-500/50 transition flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $contract->client->avatar_url }}" alt="{{ $contract->client->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <a href="{{ route('contracts.show', $contract->id) }}" class="font-bold text-sm text-slate-900 hover:text-emerald-600 transition block">
                                        {{ $contract->title }}
                                    </a>
                                    <span class="text-xs text-slate-500">Client: {{ $contract->client->name }} • Budget: ${{ number_format($contract->amount, 2) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('contracts.show', $contract->id) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold transition">
                                Open Workroom &rarr;
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No active contracts. Start applying to open projects!</p>
                    @endforelse
                </div>
            </div>

            <!-- Submitted Proposals -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Submitted Proposals ({{ $submittedProposals->count() }})</h2>

                <div class="space-y-3">
                    @forelse($submittedProposals as $proposal)
                        <div class="p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <a href="{{ route('jobs.show', $proposal->jobPosting->slug) }}" class="block font-bold text-slate-900 hover:text-emerald-600 text-sm">
                                    {{ $proposal->jobPosting->title }}
                                </a>
                                <span class="text-xs text-slate-400">Bid: ${{ number_format($proposal->bid_amount, 2) }} • Submitted {{ $proposal->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $proposal->status === 'accepted' ? 'bg-emerald-50 text-emerald-700' : ($proposal->status === 'shortlisted' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                    {{ ucfirst($proposal->status) }}
                                </span>
                                <a href="{{ route('proposals.show', $proposal->id) }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                                    View
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No submitted proposals.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recommended Jobs -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Recommended Jobs For You</h2>
                    <a href="{{ route('jobs.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Explore all &rarr;</a>
                </div>

                <div class="space-y-3">
                    @foreach($recommendedJobs as $job)
                        <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-emerald-500/40 transition">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <a href="{{ route('jobs.show', $job->slug) }}" class="font-bold text-sm text-slate-900 hover:text-emerald-600">
                                    {{ $job->title }}
                                </a>
                                <span class="font-bold text-xs text-emerald-700 shrink-0">{{ $job->budget_formatted }}</span>
                            </div>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ $job->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar / Profile Completeness & Actions -->
        <div class="lg:col-span-4 space-y-6">
            @php
                $completeness = $user->freelancerProfile?->completeness_percentage ?? 80;
                $missingSteps = $user->freelancerProfile?->missing_profile_steps ?? [];
            @endphp
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Profile Completeness</h3>
                
                <div class="flex items-center gap-3">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-200">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">{{ $user->name }}</h4>
                        <span class="text-xs font-bold text-emerald-600">${{ number_format($user->freelancerProfile->hourly_rate ?? 30, 2) }}/hr</span>
                    </div>
                </div>

                <!-- Progress -->
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="text-slate-500">Progress</span>
                        <span class="font-bold text-emerald-700">{{ $completeness }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $completeness }}%"></div>
                    </div>
                </div>

                @if(count($missingSteps) > 0)
                    <div class="space-y-1.5 pt-2 border-t border-slate-100 text-xs">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Add to complete:</span>
                        @foreach(array_slice($missingSteps, 0, 3) as $mStep)
                            <div class="flex items-center justify-between text-slate-600">
                                <span>• {{ $mStep['step'] }}</span>
                                <span class="text-emerald-600 font-bold text-[10px]">{{ $mStep['weight'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('profile.edit') }}" class="block text-center w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                    Complete Profile Sections &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
