<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'sender_type' => 'user',
            'receiver_id' => User::factory(),
            'receiver_type' => 'user',
            'content' => $this->faker->sentence,
            'is_read' => false,
        ];
    }
}
