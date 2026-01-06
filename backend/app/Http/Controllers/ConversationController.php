<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Get all conversations for the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'task'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->getOtherUser($userId);
                $unreadCount = $conversation->getUnreadCountForUser($userId);
                
                // Get last message
                $lastMessage = $conversation->messages()->latest()->first();

                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'role' => $otherUser->role,
                        'profile_photo' => $otherUser->profile_photo,
                    ],
                    'task' => $conversation->task,
                    'last_message' => $lastMessage ? [
                        'content' => $lastMessage->content,
                        'created_at' => $lastMessage->created_at,
                        'is_from_me' => $lastMessage->sender_id == $userId,
                    ] : null,
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at,
                ];
            });

        return response()->json($conversations);
    }

    /**
     * Get messages for a specific conversation
     */
    public function show($id)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($id);

        // Verify user is part of this conversation
        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'other_user' => $conversation->getOtherUser($userId),
                'task' => $conversation->task,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * Get or create a conversation between users
     */
    public function getOrCreate(Request $request)
    {
        $validated = $request->validate([
            'other_user_id' => 'required|exists:users,id',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $userId = Auth::id();
        $otherUserId = $validated['other_user_id'];
        $taskId = $validated['task_id'] ?? null;

        $conversation = Conversation::findOrCreate($userId, $otherUserId, $taskId);

        return response()->json([
            'id' => $conversation->id,
            'other_user' => $conversation->getOtherUser($userId),
            'task' => $conversation->task,
            'created_at' => $conversation->created_at,
        ]);
    }
}
