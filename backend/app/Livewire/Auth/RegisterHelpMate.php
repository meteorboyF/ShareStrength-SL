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
    
    // This will hold the selected skills as an array from the checkboxes
    public $skills = []; 

    #[Layout('components.layouts.app', ['title' => 'Register HelpMate - ShareStrength'])]
    public function render()
    {
        // 1. Pull the master list from config/skills.php
        // This ensures it matches the Post Task and Profile Edit pages perfectly.
        $availableSkills = config('skills');

        return view('livewire.auth.register-helpmate', [
            'availableSkills' => $availableSkills
        ]);
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:helpers',
            'password' => 'required|string|min:8|confirmed',
            'skills' => 'required|array|min:1', // Ensure at least one skill is selected
        ]);

        // Prevent duplicate emails across User and Admin tables
        if (User::where('email', $this->email)->exists() || Admin::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email already in use.');
            return;
        }

        $user = Helper::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            // 2. Convert the array (['Driving', 'Cooking']) to a string ("Driving, Cooking") for the DB
            'skills' => implode(', ', $this->skills), 
            'is_verified' => false,
            'is_active' => true, 
        ]);

        Auth::guard('helpmate')->login($user);

        return redirect()->intended(route('helpmate.dashboard'));
    }
}