<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Application;

class UserDashboard extends Component
{
    public $showCompleted = false;
    public $showBanner = true;
    public $openApplicantTask = null;

    #[Layout('components.layouts.app', ['title' => 'Dashboard - ShareStrength'])]
    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $tasks = Task::where('created_by', $user->id)
            ->with(['caregiver'])
            ->when(!$this->showCompleted, function ($query) {
                return $query->whereNotIn('status', ['completed', 'cancelled']);
            })
            ->latest()
            ->get();

        $tasksWithApplications = Task::where('created_by', $user->id)
            ->whereHas('applications', function ($q) {
                $q->where('status', '!=', 'rejected');
            })
            ->with(['applications.helper'])
            ->get();

        return view('livewire.dashboards.user-dashboard', [
            'user' => $user,
            'tasks' => $tasks,
            'tasksWithApplications' => $tasksWithApplications,
            'cartCount' => 0, // Placeholder
        ]);
    }

    public function toggleApplicants($taskId)
    {
        $this->openApplicantTask = $this->openApplicantTask === $taskId ? null : $taskId;
    }

    public function deleteTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::id())->firstOrFail();
        $task->delete();

        session()->flash('success', 'Task deleted successfully!');
    }

    public function repostTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::id())->firstOrFail();

        $newTask = Task::create([
            'created_by' => Auth::id(),
            'title' => $task->title,
            'description' => $task->description,
            'location' => $task->location,
            'budget' => $task->budget,
            'urgency' => $task->urgency,
            'skills_required' => $task->skills_required,
            'scheduled_at' => $task->scheduled_at,
            'status' => 'open',
        ]);

        session()->flash('success', 'Task reposted successfully!');
    }

    public function acceptApplication($applicationId)
    {
        $app = Application::where('id', $applicationId)->firstOrFail();
        // Verify ownership
        $task = Task::where('id', $app->task_id)->where('created_by', Auth::id())->firstOrFail();

        $app->update(['status' => 'accepted']);
        $task->update(['status' => 'accepted', 'caregiver_id' => $app->helper_id]);

        session()->flash('success', 'Application accepted! Task assigned.');
    }

    public function rejectApplication($applicationId)
    {
        $app = Application::where('id', $applicationId)->firstOrFail();
        $task = Task::where('id', $app->task_id)->where('created_by', Auth::id())->firstOrFail();

        $app->update(['status' => 'rejected']);

        session()->flash('success', 'Application rejected.');
    }

    public function startTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::id())->firstOrFail();

        if ($task->status !== 'accepted') {
            session()->flash('error', 'Task must be accepted before starting.');
            return;
        }

        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        session()->flash('success', 'Task started!');
    }

    public function completeTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::id())->firstOrFail();

        if ($task->status !== 'in_progress') {
            session()->flash('error', 'Task must be in progress to complete.');
            return;
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        session()->flash('success', 'Task completed!');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}
