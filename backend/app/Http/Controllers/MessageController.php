<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Get messages between current user and another user (or related to a task)
        $userId = Auth::id();
        $query = Message::query()
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            });

        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->has('contact_id')) {
            $contactId = $request->contact_id;
            $query->where(function ($q) use ($contactId) {
                $q->where('sender_id', $contactId)
                    ->orWhere('receiver_id', $contactId);
            });
        }

        return response()->json($query->with(['sender', 'receiver'])->latest()->paginate(50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
            'content' => 'required|string',
        ]);

        $senderId = Auth::id();
        $receiverId = $validated['receiver_id'];
        $taskId = $validated['task_id'] ?? null;

        // Find or create conversation
        $conversation = \App\Models\Conversation::findOrCreate($senderId, $receiverId, $taskId);

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'task_id' => $taskId,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        // Update conversation's last message timestamp
        $conversation->update(['last_message_at' => now()]);

        return response()->json($message->load(['sender', 'receiver']), 201);
    }

    public function show($id)
    {
        $message = Message::findOrFail($id);
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $message->delete();
        return response()->json(['message' => 'Message deleted']);
    }

    /**
     * Mark a single message as read
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        
        // Only the receiver can mark as read
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->update(['is_read' => true]);
        
        return response()->json(['message' => 'Message marked as read']);
    }

    /**
     * Mark all messages in a conversation as read
     */
    public function markConversationAsRead($conversationId)
    {
        $userId = Auth::id();
        $conversation = \App\Models\Conversation::findOrFail($conversationId);

        // Verify user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Mark all messages where current user is receiver as read
        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Conversation marked as read']);
    }
}
