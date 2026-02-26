<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class CustomerSupport extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';
    public $success = false;

    public function mount()
    {
        // Pre-fill data if user is logged in
        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|min:2',
            'email' => 'required|email',
            'subject' => 'required|min:5',
            'message' => 'required|min:10',
        ]);

        // Here is where you would actually send the email 
        // Mail::to('admin@sharestrength.test')->send(...);

        $this->success = true;
        $this->reset(['subject', 'message']);
    }

    #[Layout('components.layouts.app', ['title' => 'Customer Support'])]
    public function render()
    {
        return view('livewire.customer-support');
    }
}