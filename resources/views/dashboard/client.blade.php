@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Top Greeting Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Welcome back, {{ $user->name }} 👋</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">{{ $user->clientProfile?->company_name ?? 'Client Command Center' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('freelancers.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                Search Talent
            </a>
            <a href="{{ route('jobs.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Post a Project
            </a>
        </div>
    </div>

    <!-- Quick Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Open Job Posts</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $metrics['active_jobs'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Recent Proposals</span>
            <span class="text-2xl font-extrabold text-slate-900">{{ $metrics['proposals_count'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block mb-1">Active Contracts</span>
            <span class="text-2xl font-extrabold text-emerald-700">{{ $metrics['active_contracts'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Spent</span>
            <span class="text-2xl font-extrabold text-slate-900">${{ number_format($metrics['total_spent'], 2) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Posted Jobs & Proposals Column -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Active Contracts -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Active Workrooms & Contracts ({{ $activeContracts->count() }})</h2>

                <div class="space-y-3">
                    @forelse($activeContracts as $contract)
                        <div class="p-4 rounded-2xl border border-slate-200 hover:border-emerald-500/50 transition flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $contract->freelancer->avatar_url }}" alt="{{ $contract->freelancer->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <a href="{{ route('contracts.show', $contract->id) }}" class="font-bold text-sm text-slate-900 hover:text-emerald-600 transition block">
                                        {{ $contract->title }}
                                    </a>
                                    <span class="text-xs text-slate-500">Hired: {{ $contract->freelancer->name }} • Budget: ${{ number_format($contract->amount, 2) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('contracts.show', $contract->id) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-xl text-xs font-bold transition">
                                Open Workroom &rarr;
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">No active contracts running.</p>
                    @endforelse
                </div>
            </div>

            <!-- Posted Jobs -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900 uppercase tracking-wider">Your Job Postings ({{ $postedJobs->count() }})</h2>
                    <a href="{{ route('jobs.create') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">+ Post Another</a>
                </div>

                <div class="space-y-3">
                    @forelse($postedJobs as $job)
                        <div class="p-4 rounded-2xl border border-slate-200/80 hover:border-slate-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full {{ $job->status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                                <a href="{{ route('jobs.show', $job->slug) }}" class="block font-bold text-slate-900 hover:text-emerald-600 text-sm mt-1">
                                    {{ $job->title }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $job->budget_formatted }} • Posted {{ $job->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-600 font-semibold bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                    {{ $job->proposals->count() }} Proposals
                                </span>
                                <a href="{{ route('jobs.show', $job->slug) }}" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                                    Manage
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">You haven't posted any jobs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar / Company Profile & Fast Links -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Company Profile</h3>
                <div class="space-y-2 text-xs text-slate-600">
                    <p class="font-bold text-slate-900 text-sm">{{ $user->clientProfile?->company_name ?? $user->name }}</p>
                    <p class="text-slate-500">{{ $user->clientProfile?->tagline ?? 'Add your company tagline' }}</p>
                    <p class="pt-2 border-t border-slate-100">📍 {{ $user->city ? $user->city . ', ' : '' }}{{ $user->country ?? 'Worldwide' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="block text-center w-full py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition">
                    Edit Profile Details
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
