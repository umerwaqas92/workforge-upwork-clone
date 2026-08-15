@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    bidAmount: {{ $job->budget_max ?: ($job->budget_min ?: 1000) }},
    platformFeeRate: {{ \App\Models\PlatformSetting::get('platform_fee_percent', 10.0) }},
    get fee() { return Math.round(this.bidAmount * (this.platformFeeRate / 100) * 100) / 100 },
    get receive() { return Math.round((this.bidAmount - this.fee) * 100) / 100 },
    milestones: [
        { title: 'Milestone 1: Core Architecture & Setup', amount: {{ round(($job->budget_max ?: 1000) / 2) }} },
        { title: 'Milestone 2: Final Integration & Tests', amount: {{ round(($job->budget_max ?: 1000) / 2) }} }
    ],
    addMilestone() {
        this.milestones.push({ title: 'New Milestone', amount: 500 });
    },
    removeMilestone(index) {
        if (this.milestones.length > 1) {
            this.milestones.splice(index, 1);
        }
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Submit a Proposal</h1>
        <p class="text-sm text-slate-500 mt-1">Submit your bid, terms, and delivery schedule for this project.</p>
    </div>

    <!-- Job Summary Box -->
    <div class="bg-slate-900 text-white p-6 rounded-3xl mb-8 space-y-3">
        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Applying to</span>
        <h2 class="text-xl font-bold text-white">{{ $job->title }}</h2>
        <div class="flex flex-wrap gap-4 text-xs text-slate-300 pt-2 border-t border-slate-800">
            <span>Budget: <strong class="text-white">{{ $job->budget_formatted }}</strong></span>
            <span>•</span>
            <span>Category: <strong class="text-white">{{ $job->category->name ?? 'General' }}</strong></span>
            <span>•</span>
            <span>Client: <strong class="text-white">{{ $job->client->name }} ({{ $job->client->country ?? 'Worldwide' }})</strong></span>
        </div>
    </div>

    <form action="{{ route('proposals.store', $job->slug) }}" method="POST" class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        @csrf

        <!-- Bid Calculations -->
        <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">What is the full amount you'd like to bid for this job?</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bid Amount ($)</label>
                    <input type="number" step="0.01" name="bid_amount" x-model.number="bidAmount" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-base font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                        <span x-text="platformFeeRate + '%'"></span> Platform Fee
                    </label>
                    <div class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-base font-bold">
                        -$<span x-text="fee.toFixed(2)"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">You'll Receive</label>
                    <div class="w-full px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-base font-extrabold">
                        $<span x-text="receive.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimated Completion (Days)</label>
                <input type="number" name="delivery_time_days" value="{{ old('delivery_time_days', 14) }}" min="1" max="365" class="w-full sm:w-48 px-4 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
        </div>

        <!-- Milestones Builder -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Proposed Project Milestones</h3>
                    <p class="text-xs text-slate-400">Divide the project into clear payment phases.</p>
                </div>
                <button type="button" @click="addMilestone" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200/60">
                    + Add Milestone
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(m, idx) in milestones" :key="idx">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-400 w-6" x-text="idx + 1 + '.'"></span>
                        <input type="text" name="milestone_titles[]" x-model="m.title" placeholder="Milestone description" class="flex-1 text-xs rounded-xl border-slate-200 py-2 px-3">
                        <div class="w-32">
                            <input type="number" name="milestone_amounts[]" x-model.number="m.amount" placeholder="Amount ($)" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 font-semibold">
                        </div>
                        <button type="button" @click="removeMilestone(idx)" class="text-red-500 hover:text-red-700 p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Cover Letter -->
        <div>
            <label for="cover_letter" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Cover Letter *</label>
            <p class="text-xs text-slate-400 mb-2">Introduce yourself, explain your relevant background, and how you will address the client's goals.</p>
            <textarea name="cover_letter" id="cover_letter" rows="8" required placeholder="Describe your relevant experience, proposed technical strategy, and why you are the best fit for this project..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">{{ old('cover_letter') }}</textarea>
            @error('cover_letter')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Boost Proposal Option (Paid Connects) -->
        <div class="p-6 rounded-3xl bg-gradient-to-r from-purple-500/10 via-purple-500/5 to-transparent border border-purple-300/80 space-y-3">
            <div class="flex items-start gap-4">
                <input type="checkbox" name="boost_proposal" id="boost_proposal" value="1" class="mt-1 h-5 w-5 text-purple-600 focus:ring-purple-500 border-purple-300 rounded cursor-pointer">
                <div class="space-y-1">
                    <label for="boost_proposal" class="text-sm font-extrabold text-slate-900 cursor-pointer flex items-center gap-2">
                        <span>🚀 Boost this Proposal (+{{ \App\Models\PlatformSetting::get('boost_proposal_connects', 10) }} Connects)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-purple-600 text-white tracking-wider">Top of Inbox</span>
                    </label>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Promote your bid to the top of the client's proposal inbox with a highlighted purple badge to stand out from other applicants and get viewed first.
                    </p>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-4">
            <a href="{{ route('jobs.show', $job->slug) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
            <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                Submit Proposal &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
