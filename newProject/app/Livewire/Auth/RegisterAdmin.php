<?php

namespace App\Livewire\Auth;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class RegisterAdmin extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Layout('components.layouts.app', ['title' => 'Register Admin'])]
    public function render()
    {
        return view('livewire.auth.register-admin');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:10|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        Auth::guard('admin')->login($admin);
        session()->regenerate();

        return redirect()->to(route('admin.dashboard'));
    }
}
