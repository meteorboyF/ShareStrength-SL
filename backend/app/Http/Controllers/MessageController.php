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
        $user = Auth::user();
        $userId = $user->getKey();
        $userType = $user instanceof \App\Models\Helper ? 'helper' : 'user';

        $query = Message::query()
            ->where(function ($q) use ($userId, $userType) {
                $q->where(function($sub) use ($userId, $userType) {
                    $sub->where('sender_id', $userId)->where('sender_type', $userType);
                })->orWhere(function($sub) use ($userId, $userType) {
                    $sub->where('receiver_id', $userId)->where('receiver_type', $userType);
                });
            });

        // Task ID filter might be tricky if messages don't have task_id. 
        // We should rely on conversation_id or if we really need task context, join conversations.
        // But for now, if task_id argument is passed, we might ignore or add scope if migration added it?
        // Migration did NOT add task_id to messages. So we must filter by conversation's task_id if needed.
        if ($request->has('task_id')) {
             $query->whereHas('conversation', function($q) use ($request) {
                 $q->where('task_id', $request->task_id);
             });
        }

        if ($request->has('contact_id') && $request->has('contact_type')) {
            $contactId = $request->contact_id;
            $contactType = $request->contact_type;
            
            $query->where(function ($q) use ($contactId, $contactType) {
                $q->where(function($sub) use ($contactId, $contactType) {
                     $sub->where('sender_id', $contactId)->where('sender_type', $contactType);
                })->orWhere(function($sub) use ($contactId, $contactType) {
                     $sub->where('receiver_id', $contactId)->where('receiver_type', $contactType);
                });
            });
        }

        return response()->json($query->with(['sender', 'receiver'])->latest()->paginate(50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|in:user,helper',
            'task_id' => 'nullable|exists:tasks,task_id',
            'content' => 'required|string',
        ]);

        $sender = Auth::user();
        $senderId = $sender->getKey();
        $senderType = $sender instanceof \App\Models\Helper ? 'helper' : 'user';
        
        $receiverId = $validated['receiver_id'];
        $receiverType = $validated['receiver_type'];
        $taskId = $validated['task_id'] ?? null;

        // Find or create conversation
        $conversation = \App\Models\Conversation::findOrCreate(
            $senderId, $senderType, 
            $receiverId, $receiverType, 
            $taskId
        );

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'receiver_id' => $receiverId,
            'receiver_type' => $receiverType,
            // 'task_id' => $taskId, // Message table doesn't have task_id in migration, only Conversation has it? 
            // Checking migration... Migration didn't add task_id to messages, only conversation_id.
            // Wait, Message definition in earlier turns might have had it?
            // The logic below 'task_id' => $taskId in original code assumes it existed. 
            // My migration didn't add it. I should remove it or check legacy chat_messages table.
            // Assuming conversation holds the task context.
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
