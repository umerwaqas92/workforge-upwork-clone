@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    contractAmount: {{ $proposal->bid_amount }},
    milestones: {{ !empty($proposal->milestones) ? json_encode($proposal->milestones) : "[{title: 'Milestone 1: Project Setup & Initial Deliverables', amount: " . round($proposal->bid_amount / 2) . "}, {title: 'Milestone 2: Final Delivery & Handover', amount: " . round($proposal->bid_amount / 2) . "}]" }},
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
        <h1 class="text-3xl font-extrabold text-slate-900">Hire {{ $proposal->freelancer->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">Review the contract terms, configure milestones, and fund the first escrow release.</p>
    </div>

    <!-- Candidate Header Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <img src="{{ $proposal->freelancer->avatar_url }}" alt="{{ $proposal->freelancer->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $proposal->freelancer->name }}</h2>
                <p class="text-xs text-slate-500">{{ $proposal->freelancer->freelancerProfile->title ?? 'Freelance Specialist' }} • {{ $proposal->freelancer->country }}</p>
                <span class="text-xs font-semibold text-emerald-700 mt-1 inline-block">⭐ {{ $proposal->freelancer->rating }} ({{ $proposal->freelancer->rating_count }} reviews)</span>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs text-slate-400 block">Agreed Proposal Bid</span>
            <span class="text-2xl font-extrabold text-slate-900">${{ number_format($proposal->bid_amount, 2) }}</span>
        </div>
    </div>

    <form action="{{ route('contracts.hire.store', $proposal->id) }}" method="POST" class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200/80 shadow-sm space-y-8">
        @csrf

        <!-- Contract Title -->
        <div>
            <label for="title" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Contract Title *</label>
            <input type="text" name="title" id="title" required value="{{ old('title', $job->title) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm font-semibold focus:ring-2 focus:ring-emerald-500">
        </div>

        <!-- Total Contract Amount -->
        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80">
            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Total Contract Budget ($) *</label>
            <p class="text-xs text-slate-400 mb-3">Total amount to be paid over the lifecycle of this contract.</p>
            <input type="number" step="0.01" name="amount" x-model.number="contractAmount" required class="w-full sm:w-64 px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-lg font-bold">
        </div>

        <!-- Milestones Schedule -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Milestone Breakdown</h3>
                    <p class="text-xs text-slate-400">Specify milestone amounts and deliverables.</p>
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
                        <div class="w-36">
                            <input type="number" step="0.01" name="milestone_amounts[]" x-model.number="m.amount" placeholder="Amount ($)" class="w-full text-xs rounded-xl border-slate-200 py-2 px-3 font-bold">
                        </div>
                        <button type="button" @click="removeMilestone(idx)" class="text-red-500 hover:text-red-700 p-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Escrow Deposit Checkbox -->
        <div class="p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 flex items-start gap-3">
            <input type="checkbox" name="fund_first_milestone" id="fund_first_milestone" value="1" checked class="mt-1 h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 rounded">
            <label for="fund_first_milestone" class="text-xs text-emerald-900 leading-relaxed cursor-pointer">
                <strong class="block font-bold">Deposit and fund first milestone into Escrow immediately</strong>
                Funds will be held securely in escrow protection. Freelancer can begin work with confidence and you will only release funds after reviewing their submitted deliverables.
            </label>
        </div>

        <!-- Terms & Notes -->
        <div>
            <label for="terms" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Additional Terms or Confidentiality Notes (Optional)</label>
            <textarea name="terms" id="terms" rows="4" placeholder="Any specific IP transfer terms, NDA notes, or repository access details..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-slate-900 text-sm">{{ old('terms') }}</textarea>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-4">
            <a href="{{ route('proposals.show', $proposal->id) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Back to Proposal</a>
            <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md transition">
                Authorize Contract & Hire
            </button>
        </div>
    </form>
</div>
@endsection
