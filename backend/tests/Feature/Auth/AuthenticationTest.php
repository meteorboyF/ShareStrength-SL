<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: User Registration (Success)
     */
    public function test_user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'account_type' => 'pwd'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /**
     * Test Case 2: User Registration (Validation Error)
     */
    public function test_user_registration_fails_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => '', // Missing name
            'email' => 'not-an-email', // Invalid email
            'password' => 'short', // Short password
            'account_type' => 'user'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     * Test Case 3: User Login (Success)
     */
    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token'
            ]);
    }

    /**
     * Test Case 4: User Login (Invalid Credentials)
     */
    public function test_user_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'jane@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /**
     * Test Case 5: User Logout
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }

    /**
     * Test Case 6: Get Authenticated User Profile
     */
    public function test_get_authenticated_user_profile(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * Test Case 7: Update User Profile (Success)
     */
    public function test_update_user_profile_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson('/api/v1/profile', [
                'name' => 'Updated Name',
                'phone' => '1234567890',
                'location' => 'New York, USA'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'Updated Name');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'location' => 'New York, USA'
        ]);
    }

    /**
     * Test Case 8: Update User Profile (Validation Error - Email format)
     */
    public function test_update_user_profile_fails_with_invalid_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson('/api/v1/profile', [
                'email' => 'not-an-email'
            ]);

        // Assuming email update is allowed but validated, or just strictly checking profile update validation
        // Adjust based on actual controller logic. If email update is not allowed, this test might need adjustment.
        // Assuming typical update validation:
        $response->assertStatus(422); 
    }

    /**
     * Test Case 9: Upload User Photo
     */
    public function test_user_can_upload_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->postJson('/api/v1/profile/photo', [
                'photo' => $file
            ]);

        $response->assertStatus(200);
        // The path storage/profile-photos is what we expect based on typical implementations
        // Adjust the assertion based on actual controller logic if it stores elsewhere
        // But for now, just checking status and that *some* file was stored is good, 
        // or check the user record has a photo_url.
        
        $user->refresh();
        $user->refresh();
        $this->assertNotNull($user->profile_photo_url);
    }

    /**
     * Test Case 10: Unauthorized Access to Protected Route
     */
    public function test_unauthorized_access_is_blocked(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401);
    }
}
