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

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function post_task_page_can_be_rendered()
    {
        $this->actingAs($this->user, 'pwd');

        $response = $this->get(route('tasks.post'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(PostTask::class);
    }

    /** @test */
    public function user_can_post_task_with_valid_data()
    {
        $this->actingAs($this->user, 'pwd');

        Livewire::test(PostTask::class)
            ->set('title', 'Help with grocery shopping')
            ->set('description', 'Need someone to help me with weekly grocery shopping')
            ->set('selectedSkill', 'Transport & Errands')
            ->set('budget', 50.00)
            ->set('urgency', 'medium')
            ->call('postTask')
            ->assertRedirect(route('dashboard'));

        $task = Task::first();
        $this->assertNotNull($task);
        $this->assertEquals('Help with grocery shopping', $task->title);
        $this->assertEquals($this->user->id, $task->created_by);
        $this->assertEquals('open', $task->status);
        $this->assertEquals('medium', $task->urgency);
        $this->assertEquals(['Transport & Errands'], $task->required_skills);
    }

    /** @test */
    public function task_requires_title_description_and_skill()
    {
        $this->actingAs($this->user, 'pwd');

        Livewire::test(PostTask::class)
            ->set('title', '')
            ->set('description', '')
            ->set('selectedSkill', '')
            ->call('postTask')
            ->assertHasErrors(['title', 'description', 'selectedSkill']);

        $this->assertDatabaseCount('tasks', 0);
    }

    /** @test */
    public function budget_must_be_numeric()
    {
        $this->actingAs($this->user, 'pwd');

        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->set('selectedSkill', 'Other Support')
            ->set('budget', 'not-a-number')
            ->call('postTask')
            ->assertHasErrors('budget');
    }

    /** @test */
    public function budget_must_be_within_allowed_range()
    {
        $this->actingAs($this->user, 'pwd');

        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->set('selectedSkill', 'Other Support')
            ->set('budget', 5)
            ->call('postTask')
            ->assertHasErrors('budget');
    }

    /** @test */
    public function guest_cannot_access_post_task_page()
    {
        $response = $this->get(route('tasks.post'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function task_is_created_with_correct_default_status()
    {
        $this->actingAs($this->user, 'pwd');

        Livewire::test(PostTask::class)
            ->set('title', 'Test Task')
            ->set('description', 'Test Description')
            ->set('selectedSkill', 'Other Support')
            ->call('postTask');

        $task = Task::first();
        $this->assertEquals('open', $task->status);
        $this->assertEquals($this->user->id, $task->created_by);
    }
}
