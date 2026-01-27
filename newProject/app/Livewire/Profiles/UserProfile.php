<?php

namespace App\Livewire\Profiles;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class UserProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $about = '';

    #[Layout('components.layouts.app', ['title' => 'My Profile'])]
    public function render()
    {
        return view('livewire.profiles.user-profile');
    }

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        $this->name = (string) ($user?->name ?? '');
        $this->email = (string) ($user?->email ?? '');
        $this->about = (string) (($user?->profile['about'] ?? '') ?: '');
    }

    public function save()
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();

        $this->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:users,email,'.$user->getKey(),
            'about' => 'nullable|string|max:2000',
        ]);

        $profile = is_array($user->profile) ? $user->profile : [];
        $profile['about'] = $this->about;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $profile,
        ]);

        session()->flash('status', 'Profile updated.');
    }
}
