<div class="flex-1 flex flex-col h-full justify-between" wire:poll.3000ms>
    @php
        $otherUser = $conversation->participants->where('id', '!=', Auth::id())->first() ?? $conversation->participants->first();
    @endphp

    <!-- Chat Header -->
    <div class="p-4 border-b border-slate-100 bg-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ $otherUser?->avatar_url }}" alt="{{ $otherUser?->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
            <div>
                <h3 class="font-bold text-sm text-slate-900">{{ $otherUser?->name }}</h3>
                <p class="text-[11px] text-slate-500">{{ $conversation->subject ?? 'Direct Project Discussion' }}</p>
            </div>
        </div>

        @if($conversation->contract)
            <a href="{{ route('contracts.show', $conversation->contract_id) }}" class="text-xs bg-emerald-50 text-emerald-700 font-bold px-3 py-1.5 rounded-xl border border-emerald-200/60 hover:bg-emerald-100 transition">
                Contract #{{ $conversation->contract_id }} Workroom &rarr;
            </a>
        @elseif($conversation->jobPosting)
            <a href="{{ route('jobs.show', $conversation->jobPosting->slug) }}" class="text-xs bg-slate-100 text-slate-700 font-bold px-3 py-1.5 rounded-xl hover:bg-slate-200 transition">
                View Job Post &rarr;
            </a>
        @endif
    </div>

    <!-- Message Bubble Scroll Area -->
    <div class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 max-h-[460px] flex flex-col-reverse">
        @forelse($conversation->messages as $msg)
            @php $isMe = $msg->sender_id === Auth::id(); @endphp
            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                <div class="flex items-end gap-2 max-w-lg {{ $isMe ? 'flex-row-reverse' : '' }}">
                    @if(!$isMe)
                        <img src="{{ $msg->sender->avatar_url }}" alt="{{ $msg->sender->name }}" class="w-7 h-7 rounded-lg object-cover mb-1">
                    @endif
                    <div class="p-3.5 rounded-2xl text-xs leading-relaxed {{ $isMe ? 'bg-emerald-600 text-white rounded-br-xs shadow-xs' : 'bg-white text-slate-800 border border-slate-200/80 rounded-bl-xs shadow-xs' }}">
                        {{ $msg->body }}
                    </div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 px-1">
                    {{ $msg->created_at->format('h:i A') }}
                </span>
            </div>
        @empty
            <div class="text-center py-12 text-xs text-slate-400">
                No messages in this thread yet. Send a greeting to get started!
            </div>
        @endforelse
    </div>

    <!-- Message Input Bar -->
    <div class="p-4 border-t border-slate-100 bg-white">
        <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
            <input type="text" wire:model="messageBody" placeholder="Type your message here..." class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                <span>Send</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </form>
    </div>
</div>
