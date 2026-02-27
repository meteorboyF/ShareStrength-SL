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
    public $conversations = [];

    // New property for the toggle switch
    public $showRelevantOnly = true;

    #[Layout('components.layouts.app', ['title' => 'HelpMate Dashboard - ShareStrength'])]
    public function render()
    {
        $user = Auth::guard('helpmate')->user();
        $userId = $user->id;

        // Parse skills once
        $mySkills = $user->skills ? array_map('trim', explode(',', str_replace(['[', ']', '"'], '', $user->skills))) : [];

        // Get my applications
        $myApplications = Application::where('helper_id', $userId)->with('task.creator')->get();
        $appliedTaskIds = $myApplications->pluck('task_id')->toArray();

        $appliedJobs = $myApplications->map(function ($app) {
            return [
                'id' => $app->id,
                'task_id' => $app->task_id,
                'title' => $app->task->title ?? 'Unknown Task',
                'user_name' => $app->task->creator->name ?? 'Unknown',
                'status' => $app->status,
            ];
        });

        // --- UPDATED LOGIC: Available Tasks ---
        $availableTasksQuery = Task::where('status', 'open')
            ->whereNotIn('id', $appliedTaskIds)
            ->with('creator');

        // Apply skill-based filtering if the toggle is on
        if ($this->showRelevantOnly && !empty($mySkills)) {
            $availableTasksQuery->where(function ($query) use ($mySkills) {
                foreach ($mySkills as $skill) {
                    // This will find tasks where the JSON array `required_skills` contains one of my skills
                    $query->orWhereJsonContains('required_skills', $skill);
                }
            });
        }

        $availableTasks = $availableTasksQuery->latest()->get()->map(function ($task) {
            // Laravel already casts this to an array, so we just assign it.
            // The '?? []' prevents errors if the column is null.
            $requiredSkills = $task->required_skills ?? [];

            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'hourly_rate' => $task->budget ?? 0,
                // Safely get the first skill from the array
                'skill' => is_array($requiredSkills) ? $requiredSkills[0] ?? null : $requiredSkills,
                'urgency' => $task->urgency ?? 'Normal',
                'location' => $task->location,
                'user_name' => $task->creator->name ?? 'Unknown',
                'user_id' => $task->creator->id ?? null,
                'user_photo' => $task->creator->profile_photo_url ?? $task->creator->profile_photo ?? null,
            ];
        });
        // Active Jobs (unchanged)
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

        // Calculate stats (unchanged)
        $completedJobsCount = Task::where('caregiver_id', $userId)->where('status', 'completed')->count();
        $totalEarnings = Payment::where('payee_id', $userId)->where('status', 'paid')->sum('amount');

        $this->loadConversations();

        return view('livewire.dashboards.helpmate-dashboard', [
            'user' => $user,
            'skills' => $mySkills,
            'appliedJobs' => $appliedJobs,
            'availableTasks' => $availableTasks,
            'activeJobs' => $activeJobs,
            'completedJobsCount' => $completedJobsCount,
            'totalEarnings' => $totalEarnings,
        ]);
    }

    // --- All other methods (openApplyModal, confirmApply, etc.) remain unchanged ---

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
        if (!$this->selectedTaskId)
            return;

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

    public function loadConversations()
    {
        $user = Auth::guard('helpmate')->user();
        if (!$user)
            return;

        $this->conversations = \App\Models\Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'task'])
            ->get()
            ->map(function ($conv) use ($user) {
                return [
                    'id' => $conv->id,
                    'other_user' => $conv->getOtherUser($user->id, 'helper'),
                    'task' => $conv->task,
                    'last_message_at' => $conv->last_message_at,
                ];
            })->toArray();
    }
}