<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'user_one_id' => User::factory(),
            'user_one_type' => 'user',
            'user_two_id' => User::factory(),
            'user_two_type' => 'user', // Default to user-user for simplicity unless overridden
            'task_id' => null,
            'last_message_at' => now(),
        ];
    }
}
