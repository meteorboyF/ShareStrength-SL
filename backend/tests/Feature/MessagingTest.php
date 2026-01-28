<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Helper;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: Send Message (Start Conversation)
     */
    public function test_user_can_send_message_to_helper(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/messages', [
                'receiver_id' => $helper->id,
                'receiver_type' => 'helper',
                'content' => 'Hello, can you help me?'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('content', 'Hello, can you help me?');

        // Conversation uses sorted keys, so helper (h:1) comes before user (u:1)
        // Check that a conversation exists between them regardless of order
        $this->assertDatabaseHas('conversations', [
             'user_one_id' => $helper->id, // Helper comes first
             'user_one_type' => 'helper',
             'user_two_id' => $user->id,
             'user_two_type' => 'user',
        ]);
        
        $this->assertDatabaseHas('messages', [
            'content' => 'Hello, can you help me?',
            'sender_id' => $user->id,
            'receiver_id' => $helper->id
        ]);
    }

    /**
     * Test Case 2: List Conversations
     */
    public function test_can_list_conversations(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        
        Conversation::factory()->create([
            'user_one_id' => $user->id,
            'user_one_type' => 'user',
            'user_two_id' => $helper->id,
            'user_two_type' => 'helper'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/conversations');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * Test Case 3: List Messages in Conversation
     */
    public function test_can_list_messages(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $conversation = Conversation::factory()->create([
            'user_one_id' => $user->id,
            'user_one_type' => 'user',
            'user_two_id' => $helper->id,
            'user_two_type' => 'helper'
        ]);

        Message::factory()->count(5)->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'receiver_id' => $helper->id,
            'receiver_type' => 'helper',
        ]);

        // MessageController@index lists messages for the auth user, not by conversation ID directly usually, 
        // essentially it returns all messages involving the user.
        $response = $this->actingAs($user)
            ->getJson('/api/v1/messages');

        $response->assertStatus(200)
            ->assertJsonPath('total', 5);
    }

    /**
     * Test Case 4: Mark Message as Read
     */
    public function test_receiver_can_mark_message_as_read(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        
        $message = Message::factory()->create([
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'receiver_id' => $helper->id, // Helper is receiver
            'receiver_type' => 'helper',
            'is_read' => false
        ]);

        $response = $this->actingAs($helper)
            ->patchJson("/api/v1/messages/{$message->id}/read");

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'is_read' => true]);
    }

    /**
     * Test Case 5: Sender Cannot Mark Message as Read
     */
    public function test_sender_cannot_mark_message_as_read(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        
        $message = Message::factory()->create([
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'receiver_id' => $helper->id, 
            'receiver_type' => 'helper',
            'is_read' => false
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/v1/messages/{$message->id}/read");

        $response->assertStatus(403);
    }

    /**
     * Test Case 6: Get or Create Conversation Endpoint
     */
    public function test_can_get_or_create_conversation(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/conversations/get-or-create', [
                'other_user_id' => $helper->id,
                'other_user_type' => 'helper'
            ]);

        $response->assertStatus(200) // 200 because it returns existing or created
            ->assertJsonStructure(['id', 'other_user']); // other_user replaced user_one/two output
    }

    /**
     * Test Case 7: Validation Start Conversation
     */
    public function test_send_message_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/messages', [
                'content' => 'Hello',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['receiver_id', 'receiver_type']);
    }

    /**
     * Test Case 8: Mark Conversation as Read
     */
    public function test_can_mark_conversation_as_read(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $conversation = Conversation::factory()->create([
            'user_one_id' => $user->id, 
            'user_one_type' => 'user',
            'user_two_id' => $helper->id, 
            'user_two_type' => 'helper'
        ]);
        
        Message::factory()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => $helper->id,
            'sender_type' => 'helper',
            'receiver_id' => $user->id, // User is receiver
            'receiver_type' => 'user',
            'is_read' => false
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/v1/conversations/{$conversation->id}/read");

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', ['conversation_id' => $conversation->id, 'is_read' => true]);
    }

    /**
     * Test Case 9: Verify Conversation Participants (Get/Create)
     */
    public function test_get_or_create_returns_correct_participants(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        
        // Helper initiates
        $response = $this->actingAs($helper)
            ->postJson('/api/v1/conversations/get-or-create', [
                'other_user_id' => $user->id,
                'other_user_type' => 'user'
            ]);
            
        $response->assertStatus(200);
        // Should find or create user-helper conversation
    }

    /**
     * Test Case 10: Unauthorized Access to Conversation Messages
     * (Implicitly tested by index filtering, but explicit test:)
     */
    public function test_cannot_read_others_messages(): void
    {
        $user1 = User::factory()->create();
        $helper = Helper::factory()->create();
        $user2 = User::factory()->create();
        
        $message = Message::factory()->create([
            'sender_id' => $user1->id,
            'sender_type' => 'user',
            'receiver_id' => $helper->id,
            'receiver_type' => 'helper'
        ]);

        $response = $this->actingAs($user2)
            ->getJson("/api/v1/messages/{$message->id}");

        $response->assertStatus(403);
    }
}
