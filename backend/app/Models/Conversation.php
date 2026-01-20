<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_one_type',
        'user_two_id',
        'user_two_type',
        'task_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    // Relationships
    public function userOne()
    {
        return $this->morphTo(__FUNCTION__, 'user_one_type', 'user_one_id');
    }

    public function userTwo()
    {
        return $this->morphTo(__FUNCTION__, 'user_two_type', 'user_two_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Helper method to get the other user in the conversation
    public function getOtherUser($currentUserId, $currentUserType)
    {
        // Determine which user is the "other" user
        if ($this->user_one_id == $currentUserId && $this->user_one_type == $currentUserType) {
            // Current user is user_one, so return user_two
            $otherUserId = $this->user_two_id;
            $otherUserType = $this->user_two_type;
        } else {
            // Current user is user_two, so return user_one
            $otherUserId = $this->user_one_id;
            $otherUserType = $this->user_one_type;
        }

        // Manually fetch the user based on type
        if ($otherUserType === 'helper') {
            return \App\Models\Helper::find($otherUserId);
        } else {
            return \App\Models\User::find($otherUserId);
        }
    }

    // Helper method to get unread message count for a specific user
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    // Static method to find or create a conversation between two users
    public static function findOrCreate($userOneId, $userOneType, $userTwoId, $userTwoType, $taskId = null)
    {
        // Sort users to ensure consistent order (prevent duplicates)
        // Sort by Type then ID
        $u1Key = $userOneType . ':' . $userOneId;
        $u2Key = $userTwoType . ':' . $userTwoId;

        if ($u1Key > $u2Key) {
            // Swap
            [$userOneId, $userTwoId] = [$userTwoId, $userOneId];
            [$userOneType, $userTwoType] = [$userTwoType, $userOneType];
        }

        return self::firstOrCreate(
            [
                'user_one_id' => $userOneId,
                'user_one_type' => $userOneType,
                'user_two_id' => $userTwoId,
                'user_two_type' => $userTwoType,
                'task_id' => $taskId,
            ]
        );
    }
}
