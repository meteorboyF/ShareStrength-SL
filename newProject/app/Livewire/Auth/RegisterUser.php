<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class RegisterUser extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Layout('components.layouts.app', ['title' => 'Register User'])]
    public function render()
    {
        return view('livewire.auth.register-user');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        Auth::guard('web')->login($user);
        session()->regenerate();

        return redirect()->to(route('dashboard'));
    }
}
