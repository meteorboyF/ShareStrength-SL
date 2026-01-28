<?php

namespace Tests\Feature;

use App\Models\Helper;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: Create Task (Success)
     */
    public function test_user_can_create_task_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/tasks', [
                'title' => 'Help with Groceries',
                'description' => 'Need help buying weekly groceries.',
                'budget' => 50,
                'location' => '123 Main St',
                'urgency' => 'medium'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('title', 'Help with Groceries');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Help with Groceries',
            'created_by' => $user->id,
            'status' => 'open'
        ]);
    }

    /**
     * Test Case 2: Create Task (Validation Error)
     */
    public function test_create_task_validation_fails(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/tasks', [
                'title' => '', // Required
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }

    /**
     * Test Case 3: List My Tasks
     */
    public function test_user_can_list_their_tasks(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/my-tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test Case 4: Show Task Details
     */
    public function test_can_view_task_details(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $task->id);
    }

    /**
     * Test Case 5: Update Task
     */
    public function test_user_can_update_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id, 'status' => 'open']);

        $response = $this->actingAs($user)
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated Description'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('title', 'Updated Title');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);
    }

    /**
     * Test Case 6: Delete Task
     */
    public function test_user_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Test Case 7: Helper Can Accept Task
     */
    public function test_helper_can_accept_requested_task(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        
        // Task must be assigned to the helper in 'requested' status first
        $task = Task::factory()->create([
            'created_by' => $user->id, 
            'caregiver_id' => $helper->id, 
            'status' => 'requested'
        ]);

        $response = $this->actingAs($helper)
            ->putJson("/api/v1/tasks/{$task->id}/accept");

        $response->assertStatus(200)
            ->assertJsonPath('task.status', 'accepted');
        
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'accepted']);
    }

    /**
     * Test Case 8: Helper Can Start Task
     */
    public function test_helper_can_start_accepted_task(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id, 
            'caregiver_id' => $helper->id, 
            'status' => 'accepted'
        ]);

        $response = $this->actingAs($helper)
            ->putJson("/api/v1/tasks/{$task->id}/start");

        $response->assertStatus(200)
            ->assertJsonPath('task.status', 'in_progress');
            
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'in_progress']);
    }

    /**
     * Test Case 9: Helper Can Complete Task
     */
    public function test_helper_can_complete_task_and_generate_payment(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id, 
            'caregiver_id' => $helper->id, 
            'status' => 'in_progress',
            'started_at' => now()->subHours(2), // Started 2 hours ago
            'budget' => 20 // $20/hr
        ]);

        $response = $this->actingAs($helper)
            ->putJson("/api/v1/tasks/{$task->id}/complete");

        $response->assertStatus(200)
            ->assertJsonPath('task.status', 'completed');
            
        // Check Payment generation
        $this->assertDatabaseHas('payments', [
            'task_id' => $task->id,
            'payer_id' => $user->id,
            'payee_id' => $helper->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Test Case 10: Helper Cannot Accept Unassigned Task
     */
    public function test_helper_cannot_accept_unassigned_task(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $otherHelper = Helper::factory()->create();
        
        $task = Task::factory()->create([
            'created_by' => $user->id, 
            'caregiver_id' => $otherHelper->id, // Assigned to someone else
            'status' => 'requested'
        ]);

        $response = $this->actingAs($helper)
            ->putJson("/api/v1/tasks/{$task->id}/accept");

        $response->assertStatus(403);
    }
}
