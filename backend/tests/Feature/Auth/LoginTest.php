<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Helper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\Auth\Login;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_can_be_rendered()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(Login::class);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('accountType', 'pwd')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user, 'pwd');
    }

    /** @test */
    public function user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('accountType', 'pwd')
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest('pwd');
    }

    /** @test */
    public function validation_errors_are_shown_for_empty_fields()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    }

    /** @test */
    public function helpmate_is_redirected_to_helpmate_dashboard()
    {
        $helpmate = Helper::create([
            'name' => 'Test HelpMate',
            'email' => 'helpmate@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('accountType', 'helpmate')
            ->set('email', 'helpmate@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('helpmate.dashboard'));

        $this->assertAuthenticatedAs($helpmate, 'helpmate');
    }

    /** @test */
    public function admin_is_redirected_to_admin_dashboard()
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('accountType', 'admin')
            ->set('email', 'admin@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }
}
