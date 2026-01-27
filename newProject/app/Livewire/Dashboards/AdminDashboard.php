<?php

namespace App\Livewire\Dashboards;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AdminDashboard extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Admin Dashboard'])]
    public function render()
    {
        return view('livewire.dashboards.admin-dashboard', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    public function logout()
    {
        foreach (['web', 'provider', 'admin'] as $g) {
            Auth::guard($g)->logout();
        }
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to(route('home'));
    }
}
