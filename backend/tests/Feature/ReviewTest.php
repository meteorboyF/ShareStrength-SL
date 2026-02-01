<?php

namespace Tests\Feature;

use App\Models\Helper;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_review_a_helper_after_task_completion()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $helper->id,
                'reviewee_type' => 'helper',
                'rating' => 5,
                'comment' => 'Excellent service!',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'task_id' => $task->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $helper->id,
            'rating' => 5,
        ]);
    }

    /** @test */
    public function helper_can_review_a_user_after_task_completion()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($helper, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $user->id,
                'reviewee_type' => 'user',
                'rating' => 4,
                'comment' => 'Great client to work with!',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'task_id' => $task->id,
            'reviewer_id' => $helper->id,
            'reviewee_id' => $user->id,
            'rating' => 4,
        ]);
    }

    /** @test */
    public function review_requires_valid_rating_between_1_and_5()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $helper->id,
                'reviewee_type' => 'helper',
                'rating' => 6, // Invalid rating
                'comment' => 'Test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function review_requires_comment()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $helper->id,
                'reviewee_type' => 'helper',
                'rating' => 5,
                // Missing comment
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['comment']);
    }

    /** @test */
    public function cannot_review_before_task_completion()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'in_progress', // Not completed
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $helper->id,
                'reviewee_type' => 'helper',
                'rating' => 5,
                'comment' => 'Test',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function cannot_review_same_task_twice()
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create([
            'created_by' => $user->id,
            'caregiver_id' => $helper->id,
            'status' => 'completed',
        ]);

        // First review
        Review::factory()->create([
            'task_id' => $task->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $helper->id,
        ]);

        // Try to review again
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/reviews', [
                'task_id' => $task->id,
                'reviewee_id' => $helper->id,
                'reviewee_type' => 'helper',
                'rating' => 5,
                'comment' => 'Another review',
            ]);

        $response->assertStatus(409);
    }

    /** @test */
    public function can_view_helper_average_rating()
    {
        $helper = Helper::factory()->create();
        
        // Create multiple reviews
        Review::factory()->count(3)->create([
            'reviewee_id' => $helper->id,
            'reviewee_type' => 'helper',
            'rating' => 5,
        ]);

        Review::factory()->create([
            'reviewee_id' => $helper->id,
            'reviewee_type' => 'helper',
            'rating' => 4,
        ]);

        $response = $this->getJson("/api/helpers/{$helper->id}/rating");

        $response->assertStatus(200);
        $response->assertJson([
            'average_rating' => 4.75,
            'total_reviews' => 4,
        ]);
    }

    /** @test */
    public function can_view_user_average_rating()
    {
        $user = User::factory()->create();
        
        // Create multiple reviews
        Review::factory()->count(2)->create([
            'reviewee_id' => $user->id,
            'reviewee_type' => 'user',
            'rating' => 5,
        ]);

        $response = $this->getJson("/api/users/{$user->id}/rating");

        $response->assertStatus(200);
        $response->assertJson([
            'average_rating' => 5.0,
            'total_reviews' => 2,
        ]);
    }

    /** @test */
    public function can_list_all_reviews_for_a_helper()
    {
        $helper = Helper::factory()->create();
        
        Review::factory()->count(5)->create([
            'reviewee_id' => $helper->id,
            'reviewee_type' => 'helper',
        ]);

        $response = $this->getJson("/api/helpers/{$helper->id}/reviews");

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function admin_can_moderate_inappropriate_reviews()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $review = Review::factory()->create([
            'comment' => 'Inappropriate content',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/reviews/{$review->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
