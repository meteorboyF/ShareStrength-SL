<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'task_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
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
    public function getOtherUser($currentUserId)
    {
        if ($this->user_one_id == $currentUserId) {
            return $this->userTwo;
        }
        return $this->userOne;
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
    public static function findOrCreate($userOneId, $userTwoId, $taskId = null)
    {
        // Ensure user_one_id is always the smaller ID for consistency
        [$userOneId, $userTwoId] = $userOneId < $userTwoId 
            ? [$userOneId, $userTwoId] 
            : [$userTwoId, $userOneId];

        return self::firstOrCreate(
            [
                'user_one_id' => $userOneId,
                'user_two_id' => $userTwoId,
                'task_id' => $taskId,
            ]
        );
    }
}
