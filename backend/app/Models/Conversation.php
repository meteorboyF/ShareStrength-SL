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
    public function getOtherUser($currentUserId, $currentUserType = null)
    {
        $currentTypes = is_array($currentUserType) ? $currentUserType : ($currentUserType ? [$currentUserType] : []);

        if ($this->user_one_id == $currentUserId && (empty($currentTypes) || in_array($this->user_one_type, $currentTypes, true))) {
            return $this->userTwo;
        }

        if ($this->user_two_id == $currentUserId && (empty($currentTypes) || in_array($this->user_two_type, $currentTypes, true))) {
            return $this->userOne;
        }

        return $this->userOne ?? $this->userTwo;
    }

    // Helper method to get unread message count for a specific user
    public function getUnreadCountForUser($userId, $userTypes = ['user', 'helper'])
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->whereIn('receiver_type', $userTypes)
            ->where('is_read', false)
            ->count();
    }

    // Static method to find or create a conversation between two users
    public static function findOrCreate($userOneId, $userOneType, $userTwoId, $userTwoType, $taskId = null)
    {
        // Find existing conversation for this task/user pair (type-safe)
        $existing = self::where('task_id', $taskId)
            ->where(function ($q) use ($userOneId, $userOneType, $userTwoId, $userTwoType) {
                $q->where(function ($sub) use ($userOneId, $userOneType, $userTwoId, $userTwoType) {
                    $sub->where('user_one_id', $userOneId)
                        ->where('user_one_type', $userOneType)
                        ->where('user_two_id', $userTwoId)
                        ->where('user_two_type', $userTwoType);
                })->orWhere(function ($sub) use ($userOneId, $userOneType, $userTwoId, $userTwoType) {
                    $sub->where('user_one_id', $userTwoId)
                        ->where('user_one_type', $userTwoType)
                        ->where('user_two_id', $userOneId)
                        ->where('user_two_type', $userOneType);
                });
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        // Sort users to ensure consistent order (prevent duplicates)
        $u1Key = $userOneType . ':' . $userOneId;
        $u2Key = $userTwoType . ':' . $userTwoId;

        if ($u1Key > $u2Key) {
            [$userOneId, $userTwoId] = [$userTwoId, $userOneId];
            [$userOneType, $userTwoType] = [$userTwoType, $userOneType];
        }

        return self::create([
            'user_one_id' => $userOneId,
            'user_one_type' => $userOneType,
            'user_two_id' => $userTwoId,
            'user_two_type' => $userTwoType,
            'task_id' => $taskId,
        ]);
    }
}
