<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\TrustedContact;

class TrustedContacts extends Component
{
    public $contact_name = '';
    public $contact_phone = '';
    public $contact_email = '';
    public $relation = '';

    protected $rules = [
        'contact_name' => 'required|string|max:255',
        'contact_phone' => 'required|string|max:20',
        'contact_email' => 'nullable|email|max:255',
        'relation' => 'required|string|max:100',
    ];

    #[Layout('components.layouts.app', ['title' => 'Trusted Contacts - ShareStrength'])]
    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $contacts = TrustedContact::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.trusted-contacts', [
            'contacts' => $contacts,
        ]);
    }

    public function addContact()
    {
        $this->validate();

        TrustedContact::create([
            'user_id' => Auth::id(),
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'relation' => $this->relation,
        ]);

        session()->flash('success', 'Trusted contact added successfully!');

        // Reset form
        $this->reset(['contact_name', 'contact_phone', 'contact_email', 'relation']);
    }

    public function deleteContact($id)
    {
        TrustedContact::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        session()->flash('success', 'Contact deleted successfully!');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}
