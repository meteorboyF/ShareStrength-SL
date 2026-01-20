<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Payment History</h1>
            </div>
            <button wire:click="logout" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">
                Log Out
            </button>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-6 lg:px-8">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-semibold">Total Sent</p>
                        <p class="text-3xl font-bold mt-2">${{ number_format($totalSent, 2) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-semibold">Total Received</p>
                        <p class="text-3xl font-bold mt-2">${{ number_format($totalReceived, 2) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-semibold">Pending</p>
                        <p class="text-3xl font-bold mt-2">{{ $pendingPayments }}</p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex gap-4 border-b border-gray-200 pb-4">
                <button 
                    wire:click="setFilter('all')"
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filterType === 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    All Transactions
                </button>
                <button 
                    wire:click="setFilter('sent')"
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filterType === 'sent' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Sent
                </button>
                <button 
                    wire:click="setFilter('received')"
                    class="px-4 py-2 rounded-lg font-semibold transition {{ $filterType === 'received' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Received
                </button>
            </div>
        </div>

        <!-- Payment Analysis Section (Chart.js version) -->
        @if(isset($monthlySpending) && $monthlySpending->isNotEmpty() && ($filterType === 'all' || $filterType === 'sent'))
        
        <script>
            window.paymentAnalysisData = {
                monthlyLabels: @json($monthlySpending->pluck('month')),
                monthlyValues: @json($monthlySpending->pluck('total')),
                helperLabels: @json($spendingByHelper->map(fn($h) => $h->payee->name ?? 'Unknown')),
                helperValues: @json($spendingByHelper->pluck('total'))
            };
        </script>

        <div 
            x-data="{
                initCharts() {
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => this.initCharts(), 100);
                        return;
                    }

                    const data = window.paymentAnalysisData;

                    // Monthly Spending Bar Chart
                    const monthlyCtx = document.getElementById('monthlyChart');
                    if (monthlyCtx) {
                        new Chart(monthlyCtx, {
                            type: 'bar',
                            data: {
                                labels: data.monthlyLabels,
                                datasets: [{
                                    label: 'Total Spent',
                                    data: data.monthlyValues,
                                    backgroundColor: '#7c3aed', // Purple-600
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { 
                                    y: { 
                                        beginAtZero: true, 
                                        ticks: { callback: function(value) { return '$' + value; } } 
                                    } 
                                }
                            }
                        });
                    }

                    // Helper Spending Doughnut Chart
                    const helperCtx = document.getElementById('helperChart');
                    if (helperCtx && data.helperLabels.length > 0) {
                        new Chart(helperCtx, {
                            type: 'doughnut',
                            data: {
                                labels: data.helperLabels,
                                datasets: [{
                                    data: data.helperValues,
                                    backgroundColor: ['#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: { 
                                    legend: { position: 'bottom' } 
                                }
                            }
                        });
                    }
                }
            }"
            x-init="initCharts()"
            class="space-y-8 mb-12 mt-8"
        >
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <h2 class="text-2xl font-bold text-gray-800">Detailed Payment Analysis</h2>

            <!-- Monthly Bar Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-purple-100">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Monthly Spending</h3>
                <div class="h-80 w-full">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Helper Breakdown (Doughnut) -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-purple-100">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Spending by Helper</h3>
                    <div class="h-64 w-full flex justify-center">
                        <canvas id="helperChart"></canvas>
                    </div>
                </div>

                <!-- Task Breakdown Table -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-lg border border-purple-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-700">Spending by Task</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-600 font-medium">
                                <tr>
                                    <th class="px-6 py-3">Task</th>
                                    <th class="px-6 py-3">Helper</th>
                                    <th class="px-6 py-3 text-right">Total Fee</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($payments->where('status', 'paid')->take(5) as $payment)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $payment->task->title ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $payment->payee->name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-700 text-right">${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">No task data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Payments List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($payments->isEmpty())
                <div class="text-center py-16">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-gray-500 text-lg">No payment history yet.</p>
                    <p class="text-sm text-gray-400 mt-2">Your transactions will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Task</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Party</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr wire:key="payment-{{ $payment->id }}" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->payer_id === Auth::id())
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                Sent
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                Received
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $payment->task ? $payment->task->title : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($payment->payer_id === Auth::id())
                                            {{ $payment->payee ? $payment->payee->name : 'Unknown' }}
                                        @else
                                            {{ $payment->payer ? $payment->payer->name : 'Unknown' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $payment->payer_id === Auth::id() ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $payment->payer_id === Auth::id() ? '-' : '+' }}${{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $payment->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
