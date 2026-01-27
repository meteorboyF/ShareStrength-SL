<?php

namespace App\Livewire\Profiles;

use App\Models\Provider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProviderPublicProfile extends Component
{
    public Provider $provider;

    #[Layout('components.layouts.app', ['title' => 'Provider'])]
    public function render()
    {
        return view('livewire.profiles.provider-public-profile');
    }

    public function mount(int $providerId): void
    {
        $provider = Provider::find($providerId);
        if (!$provider) {
            throw new ModelNotFoundException();
        }

        $this->provider = $provider;
    }
}
