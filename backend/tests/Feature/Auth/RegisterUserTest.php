<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Auth\RegisterUser;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_page_can_be_rendered()
    {
        $response = $this->get(route('register.user'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(RegisterUser::class);
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        Livewire::test(RegisterUser::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $this->assertAuthenticated('pwd');
    }

    /** @test */
    public function registration_fails_with_mismatched_passwords()
    {
        Livewire::test(RegisterUser::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function registration_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(RegisterUser::class)
            ->set('name', 'John Doe')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors('email');
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        Livewire::test(RegisterUser::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password']);
    }
}
