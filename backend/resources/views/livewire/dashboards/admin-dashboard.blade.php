<div class="min-h-screen bg-gray-50 font-sans text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-sm text-gray-500">Welcome, {{ $user->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- NEW: Support Tickets Button -->
                <a href="{{ route('admin.support') }}" class="px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-bold hover:bg-indigo-100 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Support
                    @if($stats['open_tickets'] > 0)
                        <span class="bg-red-500 text-white text-[10px] px-1.5 rounded-full">{{ $stats['open_tickets'] }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.resources') }}" class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold hover:bg-gray-50 transition">
                    Manage Resources
                </a>
                <button wire:click="$refresh" class="px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 transition">
                    Refresh
                </button>
                <button wire:click="logout" class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-sm font-semibold hover:bg-gray-50 transition">
                    Log Out
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:px-8 space-y-8">
        @if (session()->has('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats Grid -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- NEW: Support Ticket Stat -->
            <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm relative overflow-hidden">
                <div class="absolute right-0 top-0 p-3 opacity-10">
                    <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/></svg>
                </div>
                <p class="text-xs text-red-600 font-bold uppercase">Open Tickets</p>
                <p class="text-2xl font-bold text-red-700">{{ $stats['open_tickets'] }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">PWD Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pwd_users'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">HelpMates</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['helpmates'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Active Tasks</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['active_tasks'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Completed Tasks</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_tasks'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Payments Total</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['payments_total'], 2) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Resources</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['resources'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Orders</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['orders'] }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs text-gray-500">Open Tasks</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['open_tasks'] }}</p>
            </div>
        </section>

        <!-- NEW: Recent Support Tickets Section -->
        <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Recent Support Tickets</h2>
                <a href="{{ route('admin.support') }}" class="text-xs font-bold text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 border-b bg-gray-50">
                        <tr>
                            <th class="py-2 pl-4">Status</th>
                            <th class="py-2">Subject</th>
                            <th class="py-2">User</th>
                            <th class="py-2">Date</th>
                            <th class="py-2 pr-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($latestTickets as $ticket)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 pl-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ticket->status === 'open' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="py-3 font-medium text-gray-900">{{ $ticket->subject }}</td>
                                <td class="py-3">
                                    <p class="text-gray-900 font-semibold">{{ $ticket->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $ticket->email }}</p>
                                </td>
                                <td class="py-3 text-gray-500">{{ $ticket->created_at->diffForHumans() }}</td>
                                <td class="py-3 pr-4 text-right">
                                    <a href="{{ route('admin.support') }}" class="text-indigo-600 font-bold hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-center text-gray-500" colspan="5">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Recent Tasks -->
        <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Tasks</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr>
                            <th class="py-2 pr-4">Task</th>
                            <th class="py-2 pr-4">PWD</th>
                            <th class="py-2 pr-4">HelpMate</th>
                            <th class="py-2 pr-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($latestTasks as $task)
                            <tr>
                                <td class="py-2 pr-4 font-semibold text-gray-900">{{ $task->title }}</td>
                                <td class="py-2 pr-4">{{ $task->creator->name ?? 'Unknown' }}</td>
                                <td class="py-2 pr-4">{{ $task->caregiver->name ?? 'Unassigned' }}</td>
                                <td class="py-2 pr-4">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                        {{ $task->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-500" colspan="4">No tasks yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Users & HelpMates Grid -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Latest Users</h2>
                <div class="space-y-3">
                    @forelse($latestUsers as $userRow)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $userRow->name }}</p>
                                <p class="text-xs text-gray-500">{{ $userRow->email }}</p>
                            </div>
                            <button wire:click="toggleUserActive({{ $userRow->id }})" class="text-xs font-semibold px-3 py-1 rounded-full {{ $userRow->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $userRow->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No users yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Latest HelpMates</h2>
                <div class="space-y-3">
                    @forelse($latestHelpers as $helper)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $helper->name }}</p>
                                <p class="text-xs text-gray-500">{{ $helper->email }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="toggleHelpmateVerified({{ $helper->id }})" class="text-xs font-semibold px-3 py-1 rounded-full {{ $helper->is_verified ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700' }}">
                                    {{ $helper->is_verified ? 'Verified' : 'Pending' }}
                                </button>
                                <button wire:click="toggleHelpmateActive({{ $helper->id }})" class="text-xs font-semibold px-3 py-1 rounded-full {{ $helper->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $helper->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No helpmates yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Payments & Applications -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Payments</h2>
                <div class="space-y-3">
                    @forelse($latestPayments as $payment)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $payment->task->title ?? 'Payment' }}</p>
                                <p class="text-xs text-gray-500">From {{ $payment->payer->name ?? 'Unknown' }} to {{ $payment->payee->name ?? 'Unknown' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->status }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No payments yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Applications</h2>
                <div class="space-y-3">
                    @forelse($latestApplications as $application)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $application->task->title ?? 'Application' }}</p>
                                <p class="text-xs text-gray-500">{{ $application->helper->name ?? 'Unknown' }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                {{ $application->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No applications yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Resources & Orders -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Resources</h2>
                <div class="space-y-3">
                    @forelse($latestResources as $resource)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $resource->title }}</p>
                                <p class="text-xs text-gray-500">{{ $resource->category->name ?? 'General' }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                {{ $resource->type }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No resources yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Orders</h2>
                <div class="space-y-3">
                    @forelse($latestOrders as $order)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">Order #{{ $order->id }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->name ?? 'Unknown' }}</p>
                            </div>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                ${{ number_format($order->total_amount, 2) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No orders yet.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>