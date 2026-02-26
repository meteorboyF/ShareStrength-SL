<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SupportTicket;
use Livewire\WithPagination;

class SupportTickets extends Component
{
    use WithPagination;

    public function markAsResolved($id)
    {
        $ticket = SupportTicket::find($id);
        if ($ticket) {
            $ticket->update(['status' => 'resolved']);
        }
    }

    public function deleteTicket($id)
    {
        SupportTicket::destroy($id);
    }

    public function render()
    {
        return view('livewire.admin.support-tickets', [
            'tickets' => SupportTicket::latest()->paginate(10)
        ])->layout('components.layouts.app'); // Ensure it uses your main layout
    }
}