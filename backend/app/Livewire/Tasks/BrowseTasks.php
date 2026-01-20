<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Application;

class BrowseTasks extends Component
{
    public $filterUrgency = 'all';
    public $filterCategory = 'all';
    public $searchTerm = '';

    #[Layout('components.layouts.app', ['title' => 'Browse Tasks - ShareStrength'])]
    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $tasksQuery = Task::where('status', 'open')
            ->with(['creator', 'applications']);

        if ($this->searchTerm) {
            $tasksQuery->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('location', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->filterUrgency !== 'all') {
            $tasksQuery->where('urgency', $this->filterUrgency);
        }

        if ($this->filterCategory !== 'all') {
            $tasksQuery->whereJsonContains('skills_required', $this->filterCategory);
        }

        $tasks = $tasksQuery->latest()->get();

        // Get user's applications
        $userApplications = Application::where('helper_id', Auth::id())
            ->pluck('task_id')
            ->toArray();

        return view('livewire.tasks.browse-tasks', [
            'tasks' => $tasks,
            'userApplications' => $userApplications,
        ]);
    }

    public function applyToTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        // Check if already applied
        $existingApplication = Application::where('task_id', $taskId)
            ->where('helper_id', Auth::id())
            ->first();

        if ($existingApplication) {
            session()->flash('error', 'You have already applied to this task.');
            return;
        }

        Application::create([
            'task_id' => $taskId,
            'helper_id' => Auth::id(),
            'status' => 'pending',
        ]);

        session()->flash('success', 'Application submitted successfully!');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}
