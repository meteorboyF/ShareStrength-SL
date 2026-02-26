<div class="min-h-screen bg-slate-50 font-sans p-4 sm:p-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Support Tickets</h1>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-indigo-600 hover:underline">Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Subject</th>
                            <th class="px-6 py-4 w-1/3">Message</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                        {{ $ticket->status === 'open' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ $ticket->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $ticket->email }}</p>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $ticket->subject }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 leading-relaxed">
                                    {{ Str::limit($ticket->message, 80) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <a href="mailto:{{ $ticket->email }}?subject=Re: {{ $ticket->subject }}" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Reply via Email">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </a>
                                    
                                    @if($ticket->status === 'open')
                                        <button wire:click="markAsResolved({{ $ticket->id }})" 
                                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Mark as Resolved">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @endif

                                    <button wire:click="deleteTicket({{ $ticket->id }})" wire:confirm="Delete this ticket?" 
                                            class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition" title="Delete">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($tickets->isEmpty())
                <div class="p-10 text-center text-slate-400">
                    <p>No support tickets found.</p>
                </div>
            @endif
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>