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

    #[Layout('components.layouts.app', ['title' => 'Dashboard - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('pwd')->user();

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

        $pendingPayments = Payment::where('payer_id', $user->id)
            ->where('status', 'pending')
            ->with(['task', 'payee'])
            ->latest()
            ->get();

        $cartCount = collect(session()->get('cart', []))
            ->sum(fn($item) => $item['quantity'] ?? 0);

        return view('livewire.dashboards.user-dashboard', [
            'user' => $user,
            'tasks' => $tasks,
            'tasksWithApplications' => $tasksWithApplications,
            'pendingPayments' => $pendingPayments,
            'cartCount' => $cartCount,
        ]);
    }

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

        return $this->redirect(route('messages', ['conversationId' => $conversation->id]), navigate: true);
    }

    public function logout()
    {
        Auth::guard('pwd')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}
