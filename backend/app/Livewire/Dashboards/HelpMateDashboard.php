<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class HelpMateDashboard extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Dashboard - ShareStrength'])]
    public function render()
    {
        return view('livewire.dashboards.helpmate-dashboard', [
            'user' => Auth::user(),
        ]);
    }
}
