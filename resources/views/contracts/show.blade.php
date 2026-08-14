@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ submitWorkModal: false, selectedMilestoneId: null, reviewModal: false }">
    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-500 mb-6 gap-2">
        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-800 font-medium">Contract #{{ $contract->id }}</span>
    </nav>

    <!-- Contract Workroom Header -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs mb-8 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $contract->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($contract->status === 'completed' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700') }}">
                        <span class="w-2 h-2 rounded-full {{ $contract->status === 'active' ? 'bg-emerald-500' : 'bg-blue-500' }} mr-1.5 animate-pulse"></span>
                        Status: {{ ucfirst($contract->status) }}
                    </span>
                    <span class="text-xs text-slate-400">Started {{ $contract->start_date ? $contract->start_date->format('M d, Y') : 'Recently' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">{{ $contract->title }}</h1>
            </div>

            <!-- Header Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('messages.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Chat with {{ Auth::id() === $contract->client_id ? $contract->freelancer->name : $contract->client->name }}
                </a>

                @if(Auth::id() === $contract->client_id && $contract->status === 'active')
                    <form action="{{ route('contracts.complete', $contract->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this contract completed?');">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                            ✓ Mark Contract Completed
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Financial Summary Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="text-slate-400 block">Total Budget:</span>
                <span class="text-lg font-extrabold text-slate-900">${{ number_format($contract->amount, 2) }}</span>
            </div>
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
                <span class="text-emerald-700 block font-medium">In Escrow:</span>
                <span class="text-lg font-extrabold text-emerald-800">${{ number_format($contract->total_escrow, 2) }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="text-slate-400 block">Total Paid to Date:</span>
                <span class="text-lg font-extrabold text-slate-900">${{ number_format($contract->total_paid, 2) }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="text-slate-400 block">Remaining:</span>
                <span class="text-lg font-extrabold text-slate-900">${{ number_format(max(0, $contract->amount - $contract->total_paid), 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Contract Workroom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Milestones List -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Contract Milestones</h2>
                        <p class="text-xs text-slate-500">Track deliverable submissions, escrow funds, and payment releases.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($contract->milestones as $idx => $milestone)
                        <div class="p-5 rounded-2xl border {{ $milestone->status === 'submitted_for_approval' ? 'border-amber-300 bg-amber-50/20' : ($milestone->status === 'funded_in_escrow' ? 'border-emerald-200 bg-emerald-50/20' : ($milestone->status === 'approved_and_released' ? 'border-slate-200 bg-slate-50/50' : 'border-slate-200')) }} transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold text-slate-400">Milestone {{ $idx + 1 }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $milestone->status === 'approved_and_released' ? 'bg-emerald-100 text-emerald-800' : ($milestone->status === 'submitted_for_approval' ? 'bg-amber-100 text-amber-800 animate-pulse' : ($milestone->status === 'funded_in_escrow' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600')) }}">
                                            {{ str_replace('_', ' ', $milestone->status) }}
                                        </span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-base">{{ $milestone->title }}</h3>
                                    @if($milestone->description)
                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $milestone->description }}</p>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right shrink-0">
                                    <span class="text-xl font-extrabold text-slate-900">${{ number_format($milestone->amount, 2) }}</span>
                                    @if($milestone->due_date)
                                        <span class="text-[11px] text-slate-400 block">Due: {{ $milestone->due_date->format('M d') }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Submitted Deliverables Note -->
                            @if($milestone->submission_notes)
                                <div class="p-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 my-3 leading-relaxed">
                                    <span class="font-bold text-slate-900 block mb-1">📦 Deliverables Submission Notes:</span>
                                    {{ $milestone->submission_notes }}
                                    @if($milestone->submitted_at)
                                        <span class="block text-[10px] text-slate-400 mt-1.5">Submitted on {{ $milestone->submitted_at->format('M d, Y h:i A') }}</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Milestone Control Actions -->
                            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <div class="text-slate-400">
                                    @if($milestone->funded_at)
                                        <span>Funded: {{ $milestone->funded_at->format('M d') }}</span>
                                    @endif
                                    @if($milestone->released_at)
                                        <span class="ml-2 font-medium text-emerald-600">✓ Released: {{ $milestone->released_at->format('M d, Y') }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <!-- Client: Fund Escrow -->
                                    @if(Auth::id() === $contract->client_id && $milestone->status === 'pending')
                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('contracts.milestone.fund', $milestone->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold shadow-xs transition">
                                                    Fund from Wallet (${{ number_format($milestone->amount, 2) }})
                                                </button>
                                            </form>
                                            <form action="{{ route('payments.dodo.checkout') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="amount" value="{{ $milestone->amount }}">
                                                <input type="hidden" name="purpose" value="milestone_escrow">
                                                <input type="hidden" name="reference_id" value="{{ $milestone->id }}">
                                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-bold shadow-xs transition flex items-center gap-1.5">
                                                    <span>🦤</span>
                                                    <span>Pay with Dodo</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    <!-- Freelancer: Submit Work -->
                                    @if(Auth::id() === $contract->freelancer_id && in_array($milestone->status, ['funded_in_escrow', 'pending']))
                                        <button @click="selectedMilestoneId = {{ $milestone->id }}; submitWorkModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-xs transition">
                                            Submit Work for Approval
                                        </button>
                                    @endif

                                    <!-- Client: Approve & Release Payment -->
                                    @if(Auth::id() === $contract->client_id && $milestone->status === 'submitted_for_approval')
                                        <form action="{{ route('contracts.milestone.release', $milestone->id) }}" method="POST" onsubmit="return confirm('Authorize payment release of ${{ number_format($milestone->amount, 2) }} to freelancer?');">
                                            @csrf
                                            <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold shadow transition">
                                                ✓ Approve & Release Payment
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No milestones found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Reviews Section (If Contract Completed) -->
            @if($contract->status === 'completed')
                <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Contract Reviews & Feedback</h2>
                            <p class="text-xs text-slate-500">Public ratings calculate your Job Success Score and reputation.</p>
                        </div>
                        @if(!$userReview)
                            <button @click="reviewModal = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                                + Submit Your Review
                            </button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @forelse($contract->reviews as $rev)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900">{{ $rev->reviewer->name }} ({{ $rev->role === 'client_to_freelancer' ? 'Client' : 'Freelancer' }})</span>
                                    <span class="text-amber-500 font-bold text-xs">⭐ {{ number_format($rev->rating, 1) }} / 5.0</span>
                                </div>
                                <p class="text-xs text-slate-600 italic">"{{ $rev->feedback }}"</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No feedback submitted yet. Be the first to leave a review!</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar / Participants Card -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Contract Parties</h3>

                <!-- Client Info -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Client</span>
                    <div class="flex items-center gap-3">
                        <img src="{{ $contract->client->avatar_url }}" alt="{{ $contract->client->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $contract->client->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $contract->client->clientProfile?->company_name ?? 'Client' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Freelancer Info -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Freelancer</span>
                    <div class="flex items-center gap-3">
                        <img src="{{ $contract->freelancer->avatar_url }}" alt="{{ $contract->freelancer->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">{{ $contract->freelancer->name }}</h4>
                            <p class="text-xs text-slate-500">{{ $contract->freelancer->freelancerProfile->title ?? 'Freelancer' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Work Modal (For Freelancer) -->
    <div x-show="submitWorkModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6" @click.away="submitWorkModal = false">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Submit Work for Client Approval</h3>
                <p class="text-xs text-slate-500 mt-1">Provide repository links, notes, or instructions for the client to review your deliverables.</p>
            </div>

            <form :action="'/contracts/milestones/' + selectedMilestoneId + '/submit'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Submission Notes & Links *</label>
                    <textarea name="submission_notes" rows="5" required placeholder="Explain deliverables completed, GitHub pull request URL, Figma preview, demo link, etc..." class="w-full p-3 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="submitWorkModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow">
                        Submit Deliverable
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Review Modal -->
    <div x-show="reviewModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" x-cloak>
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6" @click.away="reviewModal = false">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Leave Feedback & Review</h3>
                <p class="text-xs text-slate-500 mt-1">Rate your collaboration experience.</p>
            </div>

            <form action="{{ route('contracts.review', $contract->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Overall Rating (1 to 5 Stars) *</label>
                    <select name="rating" required class="w-full text-xs rounded-xl border-slate-300 py-2.5">
                        <option value="5.0">⭐⭐⭐⭐⭐ (5.0 - Exceptional)</option>
                        <option value="4.0">⭐⭐⭐⭐ (4.0 - Great)</option>
                        <option value="3.0">⭐⭐⭐ (3.0 - Satisfactory)</option>
                        <option value="2.0">⭐⭐ (2.0 - Poor)</option>
                        <option value="1.0">⭐ (1.0 - Unacceptable)</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Communication</label>
                        <input type="number" step="0.5" min="1" max="5" name="communication_rating" value="5.0" class="w-full text-xs rounded-xl border-slate-300 py-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Quality</label>
                        <input type="number" step="0.5" min="1" max="5" name="quality_rating" value="5.0" class="w-full text-xs rounded-xl border-slate-300 py-1.5">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Deadlines</label>
                        <input type="number" step="0.5" min="1" max="5" name="deadline_rating" value="5.0" class="w-full text-xs rounded-xl border-slate-300 py-1.5">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Written Feedback *</label>
                    <textarea name="feedback" rows="4" required placeholder="Share your experience working on this contract..." class="w-full p-3 text-xs rounded-xl border border-slate-300"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="reviewModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
