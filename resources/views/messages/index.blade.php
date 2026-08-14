@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Messages & Collaboration Hub</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Communicate with clients, freelancers, and discuss project scopes.</p>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[650px]">
        <!-- Conversations Sidebar List -->
        <div class="lg:col-span-4 border-r border-slate-100 flex flex-col">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Conversations ({{ $conversations->count() }})</span>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    @php
                        $otherParticipant = $conv->participants->where('id', '!=', Auth::id())->first() ?? $conv->participants->first();
                        $isActive = $activeConversation && $activeConversation->id === $conv->id;
                    @endphp
                    <a href="{{ route('messages.index', ['conversation' => $conv->id]) }}" class="block p-4 hover:bg-slate-50 transition {{ $isActive ? 'bg-emerald-50/40 border-l-4 border-emerald-600' : '' }}">
                        <div class="flex items-start gap-3">
                            <img src="{{ $otherParticipant?->avatar_url }}" alt="{{ $otherParticipant?->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <h4 class="font-bold text-xs text-slate-900 truncate">{{ $otherParticipant?->name ?? 'User' }}</h4>
                                    <span class="text-[10px] text-slate-400 shrink-0">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true) : '' }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $conv->subject ?? ($conv->jobPosting?->title ?? 'Direct Conversation') }}</p>
                                <p class="text-xs text-slate-600 truncate mt-1 font-medium">{{ $conv->latestMessage?->body ?? 'No messages yet' }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        No active conversations yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Room Window -->
        <div class="lg:col-span-8 flex flex-col bg-slate-50/30">
            @if($activeConversation)
                <livewire:chat-room :conversationId="$activeConversation->id" :key="$activeConversation->id" />
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Select a conversation to start chatting</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm">Choose from your list on the left to discuss proposal details or coordinate milestone deliverables.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
