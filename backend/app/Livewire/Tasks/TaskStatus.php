<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Task;
use App\Models\Payment;

class TaskStatus extends Component
{
    public $applications = [];
    public $selectedApplication = null;

    public function mount()
    {
        $this->loadApplications();
    }

    #[Layout('components.layouts.app', ['title' => 'My Applications - ShareStrength'])]
    public function render()
    {
        return view('livewire.tasks.task-status');
    }

    public function loadApplications()
    {
        $helperId = Auth::guard('helpmate')->id();

        $this->applications = Application::where('helper_id', $helperId)
            ->with(['task.creator'])
            ->latest()
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'task_id' => $app->task_id,
                    'task_status' => $app->task->status ?? null,
                    'title' => $app->task->title ?? 'Unknown Task',
                    'user_name' => $app->task->creator->name ?? 'Unknown User',
                    'description' => $app->task->description ?? '',
                    'location' => $app->task->location ?? 'Remote',
                    'skill' => $app->task->required_skills[0] ?? 'General',
                    'rate' => $app->task->budget ?? 0,
                    'status' => $app->status,
                    'started_at' => $app->task->started_at,
                    'completed_at' => $app->task->completed_at,
                    'date' => $app->created_at->format('M d, Y'),
                ];
            })
            ->toArray();
    }

    public function viewDetails($applicationId)
    {
        $this->selectedApplication = collect($this->applications)->firstWhere('id', $applicationId);
    }

    public function closeModal()
    {
        $this->selectedApplication = null;
    }

    public function startTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->status !== 'accepted') {
            session()->flash('error', 'Task must be accepted before starting.');
            return;
        }

        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        session()->flash('success', 'Task started!');
        $this->loadApplications();
        $this->closeModal();
    }

    public function completeTask($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->status !== 'in_progress') {
            session()->flash('error', 'Task must be in progress to complete.');
            return;
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Calculate hours worked
        $hoursWorked = $task->started_at->diffInMinutes($task->completed_at) / 60;
        $hoursWorked = max(0.5, ceil($hoursWorked * 2) / 2); // Round up to nearest 0.5
        $amount = $hoursWorked * $task->budget;

        // Create payment record
        Payment::create([
            'task_id' => $task->id,
            'payer_id' => $task->created_by,
            'payee_id' => Auth::guard('helpmate')->id(),
            'amount' => $amount,
            'status' => 'pending',
        ]);

        session()->flash('success', "Task completed! Payment of \${$amount} is pending.");
        $this->loadApplications();
        $this->closeModal();
    }

    public function getStatusColorClass($status)
    {
        return match($status) {
            'accepted' => 'bg-green-100 text-green-800 border-green-200',
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
