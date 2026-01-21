<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MyProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $address;
    public $location;
    public $bio;
    public $skills;
    public $disability_type;
    public $profilePhoto;
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->location = $user->location ?? '';
        $this->bio = $user->bio ?? '';
        $this->skills = $user->skills ?? '';
        $this->disability_type = $user->disability_type ?? '';
    }

    #[Layout('components.layouts.app', ['title' => 'My Profile - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check() && !Auth::guard('helpmate')->check()) {
            return redirect()->route('login');
        }

        return view('livewire.my-profile', [
            'user' => Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user(),
            'isHelpmate' => Auth::guard('helpmate')->check(),
        ]);
    }

    public function updateProfile()
    {
        $isHelpmate = Auth::guard('helpmate')->check();
        $user = $isHelpmate ? Auth::guard('helpmate')->user() : Auth::guard('pwd')->user();
        $emailRule = $isHelpmate
            ? 'unique:helpers,email,' . $user->id
            : 'unique:users,email,' . $user->id;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|' . $emailRule,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($isHelpmate) {
            $rules['skills'] = 'nullable|string|max:1000';
        } else {
            $rules['disability_type'] = 'nullable|string|max:255';
        }

        $this->validate($rules);

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'location' => $this->location,
            'bio' => $this->bio,
        ];

        if ($isHelpmate) {
            $payload['skills'] = $this->skills;
        } else {
            $payload['disability_type'] = $this->disability_type;
        }

        $user->update($payload);

        if ($this->profilePhoto) {
            $path = $this->profilePhoto->store('profile_photos', 'public');
            $user->profile_photo_url = '/storage/' . $path;
            $user->profile_photo = $user->profile_photo_url;
            $user->save();
            $this->reset('profilePhoto');
        }

        session()->flash('success', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Password updated successfully!');
    }

    public function logout()
    {
        if (Auth::guard('helpmate')->check()) {
            Auth::guard('helpmate')->logout();
        } else {
            Auth::guard('pwd')->logout();
        }
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}
