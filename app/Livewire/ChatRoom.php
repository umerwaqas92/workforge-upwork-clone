<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatRoom extends Component
{
    public $conversationId;
    public $messageBody = '';

    public function mount($conversationId)
    {
        $this->conversationId = $conversationId;
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

        $this->messageBody = '';
        $this->dispatch('messageSent');
    }

    public function render()
    {
        $conversation = Conversation::with(['participants', 'messages.sender', 'jobPosting', 'contract'])
            ->findOrFail($this->conversationId);

        return view('livewire.chat-room', [
            'conversation' => $conversation,
        ]);
    }
}
