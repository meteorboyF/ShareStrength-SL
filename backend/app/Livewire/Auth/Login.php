<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $accountType = 'pwd';

    #[Layout('components.layouts.app', ['title' => 'Login - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function mount()
    {
        $type = request()->query('type');
        if ($type && in_array($type, ['pwd', 'helpmate', 'admin'], true)) {
            $this->accountType = $type;
        }

        if (request()->routeIs('admin.login')) {
            $this->accountType = 'admin';
        }

        if (request()->routeIs('helpmate.login')) {
            $this->accountType = 'helpmate';
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
            'accountType' => 'required|in:pwd,helpmate,admin',
        ]);

        $guard = match ($this->accountType) {
            'admin' => 'admin',
            'helpmate' => 'helpmate',
            default => 'pwd',
        };

        if (Auth::guard($guard)->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::guard($guard)->user();

            // Check if account is explicitly deactivated by admin
            if ($user->is_active === false) {
                Auth::guard($guard)->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'This account is inactive.');
                return;
            }

            // Redirect based on guard type
            if ($guard === 'admin') {
                return redirect()->to(route('admin.dashboard'));
            } elseif ($guard === 'helpmate') {
                return redirect()->to(route('helpmate.dashboard'));
            } else {
                return redirect()->to(route('dashboard'));
            }
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
}
