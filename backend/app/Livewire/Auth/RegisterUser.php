<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Helper;
use App\Models\User;

class RegisterUser extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    #[Layout('components.layouts.app', ['title' => 'Register User - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.register-user');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (Helper::where('email', $this->email)->exists() || Admin::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email already in use.');
            return;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::guard('pwd')->login($user);

        return redirect()->intended(route('dashboard'));
    }
}
