<?php

namespace Tests\Feature\Tasks;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Tasks\PostTask;

class PostTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'pwd']);
        $this->actingAs($this->user);
    }

    /** @test */
    public function post_task_page_can_be_rendered()
    {
        $response = $this->get(route('tasks.post'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PostTask::class);
    }

    /** @test */
    public function user_can_post_task_with_valid_data()
    {
        Livewire::test(PostTask::class)
            ->set('title', 'Help with grocery shopping')
            ->set('description', 'Need someone to help me with weekly grocery shopping')
            ->set('location', 'New York, NY')
            ->set('budget', 50.00)
            ->set('urgency', 'medium')
            ->set('required_skills', ['Shopping', 'Driving'])
            ->call('postTask')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'Help with grocery shopping',
            'created_by' => $this->user->id,
            'status' => 'open',
        ]);
    }

    /** @test */
    public function task_requires_title_and_description()
    {
        Livewire::test(PostTask::class)
            ->set('title', '')
            ->set('description', '')
            ->call('postTask')
            ->assertHasErrors(['title', 'description']);

        $this->assertDatabaseCount('tasks', 0);
    }

    /** @test */
    public function budget_must_be_numeric()
    {
        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->set('budget', 'not-a-number')
            ->call('postTask')
            ->assertHasErrors('budget');
    }

    /** @test */
    public function scheduled_date_must_be_in_future()
    {
        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->set('scheduled_at', now()->subDay()->format('Y-m-d\TH:i'))
            ->call('postTask')
            ->assertHasErrors('scheduled_at');
    }

    /** @test */
    public function guest_cannot_access_post_task_page()
    {
        auth()->logout();

        $response = $this->get(route('tasks.post'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function task_is_created_with_correct_default_status()
    {
        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->call('postTask');

        $task = Task::first();
        $this->assertEquals('open', $task->status);
        $this->assertEquals($this->user->id, $task->created_by);
    }
}
