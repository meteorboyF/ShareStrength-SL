<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Conversation;

class HelpMateDashboard extends Component
{
    public $showApplyModal = false;
    public $selectedTaskId = null;
    public $selectedTaskTitle = '';

    #[Layout('components.layouts.app', ['title' => 'HelpMate Dashboard - ShareStrength'])]
    public function render()
    {
        $user = Auth::guard('helpmate')->user();
        $userId = $user->id;

        // Get my applications
        $myApplications = Application::where('helper_id', $userId)
            ->with('task.creator')
            ->get();

        $appliedTaskIds = $myApplications->pluck('task_id')->toArray();

        // Applied Jobs (pending applications)
        $appliedJobs = $myApplications->map(function ($app) {
            return [
                'id' => $app->id,
                'task_id' => $app->task_id,
                'title' => $app->task->title ?? 'Unknown Task',
                'user_name' => $app->task->creator->name ?? 'Unknown',
                'status' => $app->status,
            ];
        });

        // Available Tasks (open, not already applied to)
        $availableTasks = Task::where('status', 'open')
            ->whereNotIn('id', $appliedTaskIds)
            ->with('creator')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'hourly_rate' => $task->budget ?? 0,
                    'skill' => $task->required_skills[0] ?? null,
                    'urgency' => $task->urgency ?? 'Normal',
                    'user_name' => $task->creator->name ?? 'Unknown',
                    'user_id' => $task->creator->id ?? null,
                    'user_photo' => $task->creator->profile_photo_url ?? $task->creator->profile_photo ?? null,
                ];
            });

        // Active Jobs (assigned to me, in_progress or accepted)
        $activeJobs = Task::whereIn('status', ['accepted', 'in_progress'])
            ->where('caregiver_id', $userId)
            ->with('creator')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'user_id' => $task->creator->id ?? null,
                    'user_name' => $task->creator->name ?? 'Unknown',
                    'status' => $task->status,
                    'started_at' => $task->started_at,
                ];
            });

        // Calculate stats
        $completedJobsCount = Task::where('caregiver_id', $userId)
            ->where('status', 'completed')
            ->count();

        $totalEarnings = Payment::where('payee_id', $userId)
            ->where('status', 'paid')
            ->sum('amount');

        // Parse skills
        $skills = $user->skills ? explode(', ', $user->skills) : [];

        return view('livewire.dashboards.helpmate-dashboard', [
            'user' => $user,
            'skills' => $skills,
            'appliedJobs' => $appliedJobs,
            'availableTasks' => $availableTasks,
            'activeJobs' => $activeJobs,
            'completedJobsCount' => $completedJobsCount,
            'totalEarnings' => $totalEarnings,
        ]);
    }

    public function openApplyModal($taskId, $taskTitle)
    {
        $this->selectedTaskId = $taskId;
        $this->selectedTaskTitle = $taskTitle;
        $this->showApplyModal = true;
    }

    public function closeApplyModal()
    {
        $this->showApplyModal = false;
        $this->selectedTaskId = null;
        $this->selectedTaskTitle = '';
    }

    public function confirmApply()
    {
        if (!$this->selectedTaskId) return;

        // Check if already applied
        $existing = Application::where('task_id', $this->selectedTaskId)
            ->where('helper_id', Auth::guard('helpmate')->id())
            ->first();

        if ($existing) {
            session()->flash('error', 'You have already applied for this task.');
            $this->closeApplyModal();
            return;
        }

        Application::create([
            'task_id' => $this->selectedTaskId,
            'helper_id' => Auth::guard('helpmate')->id(),
            'applicant_type' => 'helper',
            'status' => 'pending',
        ]);

        session()->flash('success', 'Application submitted successfully!');
        $this->closeApplyModal();
    }

    public function startTask($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('caregiver_id', Auth::guard('helpmate')->id())
            ->first();

        if ($task && $task->status === 'accepted') {
            $task->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            session()->flash('success', 'Task started!');
        }
    }

    public function endTask($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('caregiver_id', Auth::guard('helpmate')->id())
            ->first();

        if ($task && $task->status === 'in_progress') {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Calculate payment based on hours worked
            $hoursWorked = $task->started_at->diffInMinutes($task->completed_at) / 60;
            $amount = $hoursWorked * ($task->budget ?? 0);

            Payment::create([
                'task_id' => $task->id,
                'payer_id' => $task->created_by,
                'payee_id' => Auth::guard('helpmate')->id(),
                'amount' => $amount,
                'status' => 'pending',
            ]);

            session()->flash('success', 'Task completed! Payment of $' . number_format($amount, 2) . ' calculated.');
        }
    }

    public function messageUser($userId, $taskId = null)
    {
        $helper = Auth::guard('helpmate')->user();
        $conversation = Conversation::findOrCreate(
            $helper->id,
            'helper',
            $userId,
            'user',
            $taskId
        );

        return redirect()->to(route('messages', ['conversationId' => $conversation->id]));
    }

    public function logout()
    {
        Auth::guard('helpmate')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->to(route('home'));
    }
}
