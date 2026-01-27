<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class LandingPage extends Component
{
    #[Layout('components.layouts.app', ['title' => 'Home'])]
    public function render()
    {
        return view('livewire.landing-page');
    }
}
