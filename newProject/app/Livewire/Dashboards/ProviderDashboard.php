<?php

namespace App\Livewire\Dashboards;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProviderDashboard extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Provider Dashboard'])]
    public function render()
    {
        return view('livewire.dashboards.provider-dashboard', [
            'provider' => Auth::guard('provider')->user(),
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
