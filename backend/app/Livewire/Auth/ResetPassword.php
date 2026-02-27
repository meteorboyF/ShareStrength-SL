<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Helper;

class ResetPassword extends Component
{
    public $token;
    public $email;
    public $account_type;
    public $password;
    public $password_confirmation;
    public $error = null;

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email');
        $this->account_type = request()->query('type', 'user');
    }

    public function submit()
    {
        $this->validate([
            'password' => 'required|min:8|same:password_confirmation',
        ]);

        // Verify token
        $record = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (!$record) {
            $this->error = "Invalid or expired password reset token.";
            return;
        }

        // Update Password
        if ($this->account_type === 'helpmate') {
            Helper::where('email', $this->email)->update(['password' => Hash::make($this->password)]);
        } else {
            User::where('email', $this->email)->update(['password' => Hash::make($this->password)]);
        }

        // Delete token
        DB::table('password_reset_tokens')->where('email', $this->email)->delete();

        // Redirect to login
        session()->flash('success', 'Your password has been reset successfully! You can now log in.');
        $this->redirect(route('login'));
    }

    #[Layout('components.layouts.app', ['title' => 'Reset Password - ShareStrength'])]
    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}