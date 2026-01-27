<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $accountType = 'user';

    #[Layout('components.layouts.app', ['title' => 'Login'])]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function mount(): void
    {
        $type = request()->query('type');
        if ($type && in_array($type, ['user', 'provider', 'admin'], true)) {
            $this->accountType = $type;
        }

        if (request()->routeIs('provider.login')) {
            $this->accountType = 'provider';
        }

        if (request()->routeIs('admin.login')) {
            $this->accountType = 'admin';
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'accountType' => 'required|in:user,provider,admin',
        ]);

        $guard = match ($this->accountType) {
            'provider' => 'provider',
            'admin' => 'admin',
            default => 'web',
        };

        foreach (['web', 'provider', 'admin'] as $g) {
            if ($g !== $guard) {
                Auth::guard($g)->logout();
            }
        }

        if (!Auth::guard($guard)->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'The provided credentials do not match our records.');
            return;
        }

        session()->regenerate();

        $user = Auth::guard($guard)->user();
        if ($user && property_exists($user, 'is_active') && $user->is_active === false) {
            Auth::guard($guard)->logout();
            session()->invalidate();
            session()->regenerateToken();
            $this->addError('email', 'This account is inactive.');
            return;
        }

        return redirect()->to(match ($guard) {
            'provider' => route('provider.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('dashboard'),
        });
    }
}
