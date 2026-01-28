<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Community;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: List Resources
     */
    public function test_can_list_resources(): void
    {
        Resource::factory()->count(3)->create(['is_public' => true]);

        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->getJson('/api/v1/resources');

        $response->assertStatus(200);
        // Usually dependent on pagination, assuming standard response structure
        // Adjust if apiResource returns data wrapper
    }

    /**
     * Test Case 2: Show Resource (Public)
     */
    public function test_can_view_single_resource(): void
    {
        $resource = Resource::factory()->create(['is_public' => true]);

        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->getJson("/api/v1/resources/{$resource->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $resource->id);
    }

    /**
     * Test Case 3: Public User Cannot Create Resource
     */
    public function test_public_user_cannot_create_resource(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/resources', [
                'title' => 'My Resource'
            ]);

        // Route is protected by AdminMiddleware, but check routelist.
        // Route::post('/resources', ...)->middleware(...)
        // Actually routes/api.php shows:
        // Route::middleware(AdminMiddleware)->group(...) { Route::post('/resources', ...) }
        // So public user hitting this should get 403 or 401.
        // Wait, route list had /admin/resources/upload inside middleware, 
        // and also /resources inside middleware group for create?
        // Let's verify routes in api.php.
        // Yes: Route::post('/resources', ...store) is inside AdminMiddleware group.
        
        $response->assertStatus(403);
    }

    /**
     * Test Case 4: Admin Can Create Resource
     */
    public function test_admin_can_create_resource(): void
    {
        Storage::fake('public');
        $admin = Admin::factory()->create();
        $category = ResourceCategory::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100);

        // Controller might expect file upload or just metadata?
        // ResourceController::store usually expects file.
        // Let's assume standard store.
        
        $response = $this->actingAs($admin)
            ->postJson('/api/v1/resources', [
                'title' => 'New Resource',
                'category_id' => $category->id,
                'file' => $file,
                'type' => 'pdf',
                'description' => 'A helpful resource',
                'is_public' => true
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('resources', ['title' => 'New Resource']);
    }

    /**
     * Test Case 5: List Categories
     */
    public function test_can_list_resource_categories(): void
    {
        ResourceCategory::factory()->count(3)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/resources/categories');

        $response->assertStatus(200);
    }

    /**
     * Test Case 6: Admin Can Create Category
     */
    public function test_admin_can_create_category(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/categories', [
                'name' => 'New Category',
                'icon' => 'icon-name'
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('category.name', 'New Category');

        $this->assertDatabaseHas('resource_categories', ['name' => 'New Category']);
    }

    /**
     * Test Case 7: Admin Can Update Resource
     */
    public function test_admin_can_update_resource(): void
    {
        $admin = Admin::factory()->create();
        $resource = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->putJson("/api/v1/resources/{$resource->id}", [
                'title' => 'Updated Title'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('title', 'Updated Title');
            
        $this->assertDatabaseHas('resources', ['id' => $resource->id, 'title' => 'Updated Title']);
    }

    /**
     * Test Case 8: Admin Can Delete Resource
     */
    public function test_admin_can_delete_resource(): void
    {
        $admin = Admin::factory()->create();
        $resource = Resource::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/v1/resources/{$resource->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
    }

    /**
     * Test Case 9: List Communities
     */
    public function test_can_list_communities(): void
    {
        Community::factory()->count(3)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/communities');

        $response->assertStatus(200);
    }

    /**
     * Test Case 10: Create Community Post
     */
    public function test_user_can_create_community_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/communities', [
                'content' => 'Hello Community!',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('content', 'Hello Community!');

        $this->assertDatabaseHas('communities', [
            'user_id' => $user->id,
            'content' => 'Hello Community!'
        ]);
    }
}
