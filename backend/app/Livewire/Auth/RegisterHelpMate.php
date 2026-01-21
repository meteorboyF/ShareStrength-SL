<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Helper;
use App\Models\User;

class RegisterHelpMate extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $skills = [];

    public $availableSkills = [
        "Mobility Support",
        "Driving",
        "Cooking",
        "Housekeeping",
        "Tech Support",
        "Companionship",
        "Reading Assistance"
    ];

    #[Layout('components.layouts.app', ['title' => 'Register HelpMate - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.register-helpmate');
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:helpers',
            'password' => 'required|string|min:8|confirmed',
            'skills' => 'array',
        ]);

        if (User::where('email', $this->email)->exists() || Admin::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email already in use.');
            return;
        }

        $user = Helper::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'skills' => implode(', ', $this->skills),
            'is_verified' => false,
        ]);

        Auth::guard('helpmate')->login($user);

        return redirect()->intended(route('helpmate.dashboard'));
    }
}
