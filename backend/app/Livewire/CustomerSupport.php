<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class CustomerSupport extends Component
{
    // These are the only form fields needed now
    public string $subject = '';
    public string $message = '';
    
    // This controls showing the success message
    public bool $success = false;

    /**
     * Handle the form submission to create a new support ticket.
     */
    public function submit()
    {
        $this->validate([
            'subject' => 'required',
            'message' => 'required|min:10',
        ]);

        // Automatically detect the logged-in user.
        // It checks the 'helpmate' guard first, then falls back to the default 'web' guard.
        $user = auth()->guard('helpmate')->user() ?? auth()->user();

        // Check if a user is logged in to get their details.
        // Provide defaults if somehow a guest reaches this.
        $name = $user ? $user->name : 'Anonymous Guest';
        $email = $user ? $user->email : 'guest@example.com';
        
        // The user ID is nullable because we have two separate user tables ('users' and 'helpers').
        // Storing name/email directly avoids foreign key complications.
        $userId = $user ? $user->id : null; 

        // Create the support ticket in the database.
        SupportTicket::create([
            'user_id' => null, // Storing as null to avoid foreign key errors between users/helpers tables.
            'name' => $name,
            'email' => $email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'open' // All new tickets are 'open' by default.
        ]);

        // Set the success flag to true to show the success message in the view.
        $this->success = true;

        // Reset the form fields after successful submission.
        $this->reset(['subject', 'message']);
    }

    /**
     * Render the component's view.
     */
    #[Layout('components.layouts.app', ['title' => 'Customer Support - ShareStrength'])]
    public function render()
    {
        // Pass the currently authenticated user to the view.
        // This is used to display the "Submitting as" profile badge.
        $user = auth()->guard('helpmate')->user() ?? auth()->user();
        
        return view('livewire.customer-support', [
            'user' => $user,
        ]);
    }
}