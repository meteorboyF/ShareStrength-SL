<?php

namespace App\Livewire\Auth;

use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

class RegisterProvider extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Layout('components.layouts.app', ['title' => 'Register Provider'])]
    public function render()
    {
        return view('livewire.auth.register-provider');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:providers,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $provider = Provider::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        Auth::guard('provider')->login($provider);
        session()->regenerate();

        return redirect()->to(route('provider.dashboard'));
    }
}
