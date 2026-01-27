<?php

namespace App\Livewire\Profiles;

use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProviderProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $about = '';

    #[Layout('components.layouts.app', ['title' => 'Provider Profile'])]
    public function render()
    {
        return view('livewire.profiles.provider-profile');
    }

    public function mount(): void
    {
        /** @var Provider|null $provider */
        $provider = Auth::guard('provider')->user();
        $this->name = (string) ($provider?->name ?? '');
        $this->email = (string) ($provider?->email ?? '');
        $this->about = (string) (($provider?->profile['about'] ?? '') ?: '');
    }

    public function save()
    {
        /** @var Provider $provider */
        $provider = Auth::guard('provider')->user();

        $this->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email|max:255|unique:providers,email,'.$provider->getKey(),
            'about' => 'nullable|string|max:2000',
        ]);

        $profile = is_array($provider->profile) ? $provider->profile : [];
        $profile['about'] = $this->about;

        $provider->update([
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $profile,
        ]);

        session()->flash('status', 'Profile updated.');
    }
}
