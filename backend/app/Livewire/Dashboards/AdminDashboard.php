<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class AdminDashboard extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Admin Dashboard - ShareStrength'])]
    public function render()
    {
        return view('livewire.dashboards.admin-dashboard', [
            'user' => Auth::user(),
        ]);
    }
}
