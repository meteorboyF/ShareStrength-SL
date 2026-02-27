<div class="min-h-screen bg-slate-50 font-sans text-slate-800 pb-12">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 p-2 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 leading-tight">Admin Console</h1>
                    <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Welcome, {{ $user->name }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Support Tickets Link -->
                <a href="{{ route('admin.support') }}" class="relative px-3 sm:px-4 py-2 rounded-lg bg-slate-50 text-slate-700 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Support
                    @if($stats['open_tickets'] > 0)
                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] h-5 w-5 flex items-center justify-center rounded-full border-2 border-white shadow-sm">{{ $stats['open_tickets'] }}</span>
                    @endif
                </a>

                <!-- Resources Link -->
                <a href="{{ route('admin.resources') }}" class="hidden sm:flex px-4 py-2 rounded-lg bg-white border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Resources
                </a>

                <!-- Refresh Button -->
                <button wire:click="$refresh" class="p-2 sm:px-4 sm:py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition flex items-center gap-2" title="Refresh Dashboard">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <span class="hidden sm:inline">Refresh</span>
                </button>

                <!-- Logout -->
                <button wire:click="logout" class="p-2 sm:px-3 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Log Out">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:px-8 space-y-8">
        
        @if (session()->has('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-800 text-sm font-medium flex items-center gap-3 animate-fade-in-up">
                <div class="bg-emerald-100 p-1 rounded-full text-emerald-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                {{ session('success') }}
            </div>
        @endif

        <!-- Key Metrics Grid -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            
            <!-- Open Tickets (Red/Alert Focus) -->
            <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 bg-red-50 p-6 rounded-full group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-red-200" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">Open Tickets</p>
                    <p class="text-3xl font-black text-slate-900">{{ $stats['open_tickets'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">PWD Users</p>
                        <p class="text-3xl font-black text-slate-900">{{ $stats['pwd_users'] }}</p>
                    </div>
                    <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg></div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">HelpMates</p>
                        <p class="text-3xl font-black text-slate-900">{{ $stats['helpmates'] }}</p>
                    </div>
                    <div class="bg-sky-50 p-2 rounded-lg text-sky-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Active Tasks</p>
                        <p class="text-3xl font-black text-slate-900">{{ $stats['active_tasks'] }}</p>
                    </div>
                    <div class="bg-amber-50 p-2 rounded-lg text-amber-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg></div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 relative overflow-hidden group">
                <div class="absolute -right-2 -bottom-2 bg-emerald-50 p-6 rounded-full group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Total Paid</p>
                    <p class="text-2xl font-black text-slate-900">${{ number_format($stats['payments_total'], 2) }}</p>
                </div>
            </div>
            
            <!-- Secondary Stats row (Smaller) -->
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex justify-between items-center col-span-2 md:col-span-1 lg:col-span-2">
                <span class="text-sm font-semibold text-slate-500">Completed Tasks: <span class="text-slate-900 ml-1">{{ $stats['completed_tasks'] }}</span></span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-500">Open Tasks: <span class="text-slate-900 ml-1">{{ $stats['open_tasks'] }}</span></span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-500">Resources: <span class="text-slate-900 ml-1">{{ $stats['resources'] }}</span></span>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-500">Orders: <span class="text-slate-900 ml-1">{{ $stats['orders'] }}</span></span>
            </div>
        </section>

        <!-- Full Width Tables Area -->
        <div class="space-y-8">
            
            <!-- Support Tickets -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        Recent Support Tickets
                    </h2>
                    <a href="{{ route('admin.support') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Manage All &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Submitted By</th>
                                <th class="px-6 py-4">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($latestTickets as $ticket)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-3">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $ticket->status === 'open' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 font-medium text-slate-900">{{ $ticket->subject }}</td>
                                    <td class="px-6 py-3">
                                        <p class="text-slate-900 font-semibold">{{ $ticket->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $ticket->email }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-slate-500">{{ $ticket->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">No support tickets found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Tasks -->
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Recent Tasks Activity
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-4">Task Details</th>
                                <th class="px-6 py-4">Posted By (PWD)</th>
                                <th class="px-6 py-4">Assigned HelpMate</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($latestTasks as $task)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $task->title }}</td>
                                    <td class="px-6 py-4">
                                        @if($task->creator)
                                            <div class="flex items-center gap-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($task->creator->name) }}&color=4F46E5&background=EEF2FF" class="w-6 h-6 rounded-full">
                                                <span class="text-slate-700">{{ $task->creator->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($task->caregiver)
                                            <div class="flex items-center gap-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($task->caregiver->name) }}&color=0284c7&background=e0f2fe" class="w-6 h-6 rounded-full">
                                                <span class="text-slate-700">{{ $task->caregiver->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic px-2 py-1 bg-slate-100 rounded text-xs">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                                            {{ $task->status === 'open' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $task->status === 'completed' ? 'bg-slate-100 text-slate-600' : '' }}">
                                            {{ str_replace('_', ' ', $task->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">No recent tasks.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Two Column Lists Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Users -->
            <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    New PWD Users
                </h2>
                <div class="space-y-3">
                    @forelse($latestUsers as $userRow)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <img src="{{ $userRow->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($userRow->name).'&color=4F46E5&background=EEF2FF' }}" class="w-10 h-10 rounded-full border border-slate-200">
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $userRow->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $userRow->email }}</p>
                                </div>
                            </div>
                            <button wire:click="toggleUserActive({{ $userRow->id }})" class="text-xs font-bold px-3 py-1.5 rounded-lg border transition-colors {{ $userRow->is_active ? 'bg-green-50 text-green-700 border-green-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200' : 'bg-red-50 text-red-700 border-red-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                                {{ $userRow->is_active ? 'ACTIVE' : 'SUSPENDED' }}
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">No users found.</p>
                    @endforelse
                </div>
            </section>

            <!-- HelpMates -->
            <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    New HelpMates
                </h2>
                <div class="space-y-3">
                    @forelse($latestHelpers as $helper)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <img src="{{ $helper->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($helper->name).'&color=0284c7&background=e0f2fe' }}" class="w-10 h-10 rounded-full border border-slate-200">
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $helper->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $helper->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleHelpmateVerified({{ $helper->id }})" class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg border transition-colors {{ $helper->is_verified ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-sky-50 hover:text-sky-700' }}">
                                    {{ $helper->is_verified ? 'VERIFIED' : 'APPROVE' }}
                                </button>
                                <button wire:click="toggleHelpmateActive({{ $helper->id }})" title="Toggle Active Status" class="p-1.5 rounded-lg border transition-colors {{ $helper->is_active ? 'bg-green-50 text-green-600 border-green-200 hover:bg-red-50 hover:text-red-600' : 'bg-red-50 text-red-600 border-red-200 hover:bg-green-50 hover:text-green-600' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">No helpmates found.</p>
                    @endforelse
                </div>
            </section>

            <!-- Payments -->
            <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Recent Payments
                </h2>
                <div class="space-y-3">
                    @forelse($latestPayments as $payment)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="font-bold text-slate-900 text-sm">{{ $payment->task->title ?? 'Platform Payment' }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $payment->payer->name ?? 'User' }} &rarr; {{ $payment->payee->name ?? 'Helper' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-emerald-600">${{ number_format($payment->amount, 2) }}</p>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">{{ $payment->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">No recent payments.</p>
                    @endforelse
                </div>
            </section>

            <!-- Marketplace Orders -->
            <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    Recent Shop Orders
                </h2>
                <div class="space-y-3">
                    @forelse($latestOrders as $order)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="font-bold text-slate-900 text-sm">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">by {{ $order->user->name ?? 'Guest User' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2.5 py-1 rounded-lg">
                                    ${{ number_format($order->total_amount, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">No marketplace orders yet.</p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</div>