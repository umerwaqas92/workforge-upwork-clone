<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ChatRoom extends Component
{
    public $conversationId;
    public $messageBody = '';

    public function mount($conversationId)
    {
        $this->conversationId = $conversationId;
    }

    /**
     * Broadcast that current user is typing
     */
    public function userTyping()
    {
        if (Auth::check()) {
            Cache::put("conversation_{$this->conversationId}_typing_" . Auth::id(), now()->timestamp, 5);
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'messageBody' => 'required|string|min:1',
        ]);

        $conversation = Conversation::findOrFail($this->conversationId);
        
        // Security check
        if (!$conversation->participants->contains('id', Auth::id()) && !Auth::user()->isAdmin()) {
            abort(403);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $this->messageBody,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Clear typing status immediately
        Cache::forget("conversation_{$this->conversationId}_typing_" . Auth::id());

        $this->messageBody = '';
        $this->dispatch('messageSent');
    }

    public function render()
    {
        $conversation = Conversation::with(['participants', 'messages.sender', 'jobPosting', 'contract'])
            ->findOrFail($this->conversationId);

        // Determine if other participant is currently typing
        $otherUser = $conversation->participants->where('id', '!=', Auth::id())->first();
        $isOtherTyping = false;
        $typingUserName = '';

        if ($otherUser) {
            $lastTyped = Cache::get("conversation_{$this->conversationId}_typing_" . $otherUser->id);
            if ($lastTyped && (now()->timestamp - $lastTyped) <= 4) {
                $isOtherTyping = true;
                $typingUserName = $otherUser->name;
            }
        }

        return view('livewire.chat-room', [
            'conversation' => $conversation,
            'isOtherTyping' => $isOtherTyping,
            'typingUserName' => $typingUserName,
        ]);
    }
}
