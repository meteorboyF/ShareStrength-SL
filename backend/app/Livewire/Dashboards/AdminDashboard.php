<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Application;
use App\Models\Helper;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\Task;
use App\Models\User;

class AdminDashboard extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Admin Dashboard - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $stats = [
            'pwd_users' => User::count(),
            'helpmates' => Helper::count(),
            'open_tasks' => Task::where('status', 'open')->count(),
            'active_tasks' => Task::whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'payments_total' => (float) Payment::where('status', 'paid')->sum('amount'),
            'resources' => Resource::count(),
            'orders' => Order::count(),
        ];

        $latestUsers = User::latest()->take(5)->get();
        $latestHelpers = Helper::latest()->take(5)->get();
        $latestTasks = Task::with(['creator', 'caregiver'])->latest()->take(10)->get();
        $latestPayments = Payment::with(['payer', 'payee', 'task'])->latest()->take(10)->get();
        $latestResources = Resource::with('category')->latest()->take(5)->get();
        $latestOrders = Order::with('user')->latest()->take(5)->get();
        $latestApplications = Application::with(['task.creator', 'helper'])->latest()->take(5)->get();

        return view('livewire.dashboards.admin-dashboard', [
            'user' => Auth::guard('admin')->user(),
            'stats' => $stats,
            'latestUsers' => $latestUsers,
            'latestHelpers' => $latestHelpers,
            'latestTasks' => $latestTasks,
            'latestPayments' => $latestPayments,
            'latestResources' => $latestResources,
            'latestOrders' => $latestOrders,
            'latestApplications' => $latestApplications,
        ]);
    }

    public function toggleUserActive($userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'User status updated.');
    }

    public function toggleHelpmateActive($helperId)
    {
        $helper = Helper::findOrFail($helperId);
        $helper->update(['is_active' => !$helper->is_active]);
        session()->flash('success', 'HelpMate status updated.');
    }

    public function toggleHelpmateVerified($helperId)
    {
        $helper = Helper::findOrFail($helperId);
        $helper->update(['is_verified' => !$helper->is_verified]);
        session()->flash('success', 'HelpMate verification updated.');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(route('home'));
    }
}
