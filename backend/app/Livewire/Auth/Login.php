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

    #[Layout('components.layouts.app', ['title' => 'Login - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'caregiver') {
                return redirect()->intended(route('helpmate.dashboard'));
            } else {
                return redirect()->intended(route('dashboard'));
            }
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
}
