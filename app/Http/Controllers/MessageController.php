<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with(['participants', 'latestMessage', 'jobPosting', 'contract'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversationId = $request->query('conversation') ?? ($conversations->first()?->id ?? null);
        $activeConversation = null;

        if ($activeConversationId) {
            $activeConversation = Conversation::with(['participants', 'messages.sender', 'jobPosting', 'contract'])
                ->findOrFail($activeConversationId);

            // Verify user belongs to conversation
            if (!$activeConversation->participants->contains('id', $user->id) && !$user->isAdmin()) {
                abort(403);
            }
        }

        return view('messages.index', compact('conversations', 'activeConversation'));
    }

    public function startConversation(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'job_id' => 'nullable|exists:job_postings,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|min:1',
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($validated['recipient_id']);

        // Find or create conversation
        $conversation = Conversation::whereHas('participants', function ($q) use ($sender) {
            $q->where('users.id', $sender->id);
        })->whereHas('participants', function ($q) use ($recipient) {
            $q->where('users.id', $recipient->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'job_posting_id' => $validated['job_id'] ?? null,
                'subject' => $validated['subject'] ?? 'Direct Conversation',
                'last_message_at' => now(),
            ]);
            $conversation->participants()->attach([$sender->id, $recipient->id]);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('messages.index', ['conversation' => $conversation->id])->with('success', 'Message sent!');
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        if (!$conversation->participants->contains('id', $user->id) && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|min:1',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Message sent.');
    }
}
