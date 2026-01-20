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
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $userId = $user->getKey();
            $userType = $user instanceof \App\Models\Helper ? 'helper' : 'user';

            $conversations = Conversation::where(function($q) use ($userId, $userType) {
                    $q->where('user_one_id', $userId)->where('user_one_type', $userType);
                })
                ->orWhere(function($q) use ($userId, $userType) {
                    $q->where('user_two_id', $userId)->where('user_two_type', $userType);
                })
                ->orderBy('last_message_at', 'desc')
                ->get()
                ->map(function ($conversation) use ($userId, $userType) {
                    try {
                        // Manually load the other user
                        $otherUser = $conversation->getOtherUser($userId, $userType);
                        
                        // Get unread count
                        $unreadCount = $conversation->messages()
                            ->where('receiver_id', $userId)
                            ->where('receiver_type', $userType)
                            ->where('is_read', false)
                            ->count();

                        // Get last message
                        $lastMessage = $conversation->messages()->latest()->first();

                        return [
                            'id' => $conversation->id,
                            'other_user' => $otherUser ? [
                                'id' => $otherUser->getKey(),
                                'name' => $otherUser->name ?? 'Unknown',
                                'role' => ($otherUser instanceof \App\Models\Helper) ? 'caregiver' : 'pwd',
                                'profile_photo' => $otherUser->profile_photo ?? null,
                                'type' => ($otherUser instanceof \App\Models\Helper) ? 'helper' : 'user',
                            ] : null,
                            'task' => $conversation->task,
                            'last_message' => $lastMessage ? [
                                'content' => $lastMessage->content,
                                'created_at' => $lastMessage->created_at,
                                'is_from_me' => $lastMessage->sender_id == $userId && $lastMessage->sender_type == $userType,
                            ] : null,
                            'unread_count' => $unreadCount,
                            'last_message_at' => $conversation->last_message_at,
                            'created_at' => $conversation->created_at,
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error mapping conversation: ' . $e->getMessage());
                        return null;
                    }
                })->filter(); // Remove null entries

            return response()->json($conversations);
        } catch (\Exception $e) {
            \Log::error('ConversationController index error: ' . $e->getMessage() . ' | ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to fetch conversations',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Get messages for a specific conversation
     */
    public function show($id)
    {
        $user = Auth::user();
        $userId = $user->getKey();
        $userType = $user instanceof \App\Models\Helper ? 'helper' : 'user';

        $conversation = Conversation::findOrFail($id);

        // Verify user is part of this conversation using polymorphic types
        $isUserOne = $conversation->user_one_id == $userId && $conversation->user_one_type == $userType;
        $isUserTwo = $conversation->user_two_id == $userId && $conversation->user_two_type == $userType;

        if (!$isUserOne && !$isUserTwo) {
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
            'other_user_id' => 'required|integer',
            'other_user_type' => 'required|in:user,helper',
            'task_id' => 'nullable|exists:tasks,task_id',
        ]);

        $user = Auth::user();
        $userId = $user->getKey();
        $userType = $user instanceof \App\Models\Helper ? 'helper' : 'user';
        
        $otherUserId = $validated['other_user_id'];
        $otherUserType = $validated['other_user_type'];
        $taskId = $validated['task_id'] ?? null;

        $conversation = Conversation::findOrCreate($userId, $userType, $otherUserId, $otherUserType, $taskId);

        $otherUser = $conversation->getOtherUser($userId, $userType);
        
        return response()->json([
            'id' => $conversation->id,
            'other_user' => [
                'id' => $otherUser->getKey(),
                'name' => $otherUser->name,
                'role' => ($otherUser instanceof \App\Models\Helper) ? 'caregiver' : 'pwd',
                'profile_photo' => $otherUser->profile_photo,
                'type' => ($otherUser instanceof \App\Models\Helper) ? 'helper' : 'user',
            ],
            'task' => $conversation->task,
            'created_at' => $conversation->created_at,
        ]);
    }
}
