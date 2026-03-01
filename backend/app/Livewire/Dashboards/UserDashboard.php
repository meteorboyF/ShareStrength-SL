<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Application;
use App\Models\Conversation;
use App\Models\Payment;

class UserDashboard extends Component
{
    public $showCompleted = false;
    public $showBanner = true;
    public $openApplicantTask = null;
    public $conversations = [];

    // Review Modal Props
    public $showRateModal = false;
    public $ratingScore = 0;
    public $ratingComment = '';
    public $ratingTaskTitle = '';

    // Task Approval Props
    public $approvalTask = null;

    #[Layout('components.layouts.app', ['title' => 'Dashboard - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('pwd')->user();

        $tasks = Task::where('created_by', $user->id)
            ->with(['caregiver'])
            ->when(
                $this->showCompleted,
                // IF toggled ON: only show completed and cancelled
                fn($query) => $query->whereIn('status', ['completed', 'cancelled']),
                // IF toggled OFF: only show everything else
                fn($query) => $query->whereNotIn('status', ['completed', 'cancelled'])
            )
            ->latest()
            ->get();

        $tasksWithApplications = Task::where('created_by', $user->id)
            // Fix: Only show tasks that are NOT completed or cancelled
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereHas('applications', function ($q) {
                $q->where('status', '!=', 'rejected');
            })
            ->with(['applications.helper'])
            ->get();

        $pendingPayments = Payment::where('payer_id', $user->id)
            ->where('status', 'pending')
            ->with(['task', 'payee'])
            ->latest()
            ->get();

        $cartCount = collect(session()->get('cart', []))
            ->sum(fn($item) => $item['quantity'] ?? 0);

        $this->loadConversations();

        return view('livewire.dashboards.user-dashboard', [
            'user' => $user,
            'tasks' => $tasks,
            'tasksWithApplications' => $tasksWithApplications,
            'pendingPayments' => $pendingPayments,
            'cartCount' => $cartCount,
        ]);
    }

    // --- Task Approval Logic (Poller) ---

    public function checkApprovals()
    {
        $this->approvalTask = Task::where('created_by', Auth::guard('pwd')->id())
            ->whereIn('status', ['pending_start', 'pending_end'])
            ->with('caregiver')
            ->first();
    }

    public function approveStart()
    {
        if ($this->approvalTask && $this->approvalTask->status === 'pending_start') {
            $this->approvalTask->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            $this->approvalTask = null; // Close modal
            session()->flash('success', 'Work started! Timer is running.');
        }
    }

    public function approveEnd()
    {
        if ($this->approvalTask && $this->approvalTask->status === 'pending_end') {
            $task = $this->approvalTask;

            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Calculate Payment
            $hoursWorked = $task->started_at->diffInMinutes($task->completed_at) / 60;
            // Minimum 15 mins charge
            if ($hoursWorked < 0.25)
                $hoursWorked = 0.25;

            $amount = $hoursWorked * ($task->budget ?? 0);

            Payment::create([
                'task_id' => $task->id,
                'payer_id' => $task->created_by,
                'payee_id' => $task->caregiver_id,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            $this->approvalTask = null; // Close modal
            session()->flash('success', 'Task completed! Payment calculated.');
        }
    }

    public function rejectRequest()
    {
        // Revert status back if user says "No"
        if ($this->approvalTask) {
            $newStatus = $this->approvalTask->status === 'pending_start' ? 'accepted' : 'in_progress';
            $this->approvalTask->update(['status' => $newStatus]);
            $this->approvalTask = null;
        }
    }

    // --- End Task Approval Logic ---

    public function toggleApplicants($taskId)
    {
        $this->openApplicantTask = $this->openApplicantTask === $taskId ? null : $taskId;
    }

    public function deleteTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::guard('pwd')->id())->firstOrFail();
        $task->delete();

        session()->flash('success', 'Task deleted successfully!');
    }

    public function repostTask($taskId)
    {
        $task = Task::where('id', $taskId)->where('created_by', Auth::guard('pwd')->id())->firstOrFail();

        $newTask = Task::create([
            'created_by' => Auth::guard('pwd')->id(),
            'title' => $task->title,
            'description' => $task->description,
            'location' => $task->location,
            'budget' => $task->budget,
            'urgency' => $task->urgency,
            'required_skills' => $task->required_skills,
            'scheduled_at' => $task->scheduled_at,
            'status' => 'open',
        ]);

        session()->flash('success', 'Task reposted successfully!');
    }

    public function acceptApplication($applicationId)
    {
        $app = Application::where('id', $applicationId)->firstOrFail();
        // Verify ownership
        $task = Task::where('id', $app->task_id)->where('created_by', Auth::guard('pwd')->id())->firstOrFail();

        $app->update(['status' => 'accepted']);
        $task->update(['status' => 'accepted', 'caregiver_id' => $app->helper_id]);

        session()->flash('success', 'Application accepted! Task assigned.');
    }

    public function rejectApplication($applicationId)
    {
        $app = Application::where('id', $applicationId)->firstOrFail();
        $task = Task::where('id', $app->task_id)->where('created_by', Auth::guard('pwd')->id())->firstOrFail();

        $app->update(['status' => 'rejected']);

        session()->flash('success', 'Application rejected.');
    }

    public function messageHelper($helperId, $taskId = null)
    {
        $user = Auth::guard('pwd')->user();
        $conversation = Conversation::findOrCreate(
            $user->id,
            'user',
            $helperId,
            'helper',
            $taskId
        );

        return redirect()->to(route('messages', ['conversationId' => $conversation->id]));
    }

    public function logout()
    {
        Auth::guard('pwd')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(route('home'));
    }

    public function loadConversations()
    {
        $user = Auth::guard('pwd')->user();
        if (!$user)
            return;

        $this->conversations = \App\Models\Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'task'])
            ->get()
            ->map(function ($conv) use ($user) {
                return [
                    'id' => $conv->id,
                    'other_user' => $conv->getOtherUser($user->id, 'user'),
                    'task' => $conv->task,
                    'last_message_at' => $conv->last_message_at,
                ];
            })->toArray();
    }


    public function openRateModal($taskId, $taskTitle)
    {
        $this->ratingTaskTitle = $taskTitle;
        $this->ratingScore = 0;
        $this->ratingComment = '';
        $this->showRateModal = true;
    }

    public function setRating($score)
    {
        $this->ratingScore = $score;
    }

    public function submitReview()
    {
        // For the demo video, we just simulate a successful submission
        $this->showRateModal = false;

        // Reset
        $this->ratingScore = 0;
        $this->ratingComment = '';

        session()->flash('success', 'Review submitted successfully! Thank you for your feedback.');
    }
}