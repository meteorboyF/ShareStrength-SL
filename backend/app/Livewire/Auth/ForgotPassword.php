<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Helper;

class ForgotPassword extends Component
{
    public $email = '';
    public $account_type = 'user';
    public $resetLink = null;
    public $error = null;

    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
            'account_type' => 'required|in:user,helpmate'
        ]);

        $this->error = null;
        $this->resetLink = null;

        // 1. Check if user exists in the correct table
        $userExists = $this->account_type === 'user' 
            ? User::where('email', $this->email)->exists()
            : Helper::where('email', $this->email)->exists();

        if (!$userExists) {
            $this->error = "We can't find an account with that email address.";
            return;
        }

        // 2. Generate a secure token
        $token = Str::random(60);

        // 3. Save to password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'token' => $token, // Storing plain for custom verification (Hash::make is standard, but this is simpler for multi-auth)
                'created_at' => now()
            ]
        );

        // 4. Generate the link (Demo Mode: Show on screen instead of emailing)
        $this->resetLink = route('password.reset', ['token' => $token, 'email' => $this->email, 'type' => $this->account_type]);
    }

    #[Layout('components.layouts.app', ['title' => 'Forgot Password - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}