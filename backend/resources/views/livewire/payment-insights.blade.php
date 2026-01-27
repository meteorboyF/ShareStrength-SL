<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header -->
        <header>
            <a href="{{ route('payment-history') }}" class="text-purple-600 hover:text-purple-700 text-sm font-semibold mb-2 inline-block">
                &larr; Back to Payment History
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900">Detailed Payment Analysis</h1>
            <p class="mt-1 text-gray-500">In-depth look at your spending habits.</p>
        </header>

        <!-- Monthly Bar Chart -->
        <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Monthly Spending</h2>
            <div class="h-80 w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </section>

        <!-- Split View: Helper Breakdown & Task Table -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            <!-- Helper Breakdown (Doughnut) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Spending by Helper</h2>
                <div class="h-56 w-56 mx-auto mb-6">
                    <canvas id="helperChart"></canvas>
                </div>
                <ul class="space-y-3">
                    @foreach($helperData as $helper)
                        <li class="flex justify-between items-center text-sm border-b border-gray-100 last:border-0 pb-2 last:pb-0">
                            <span class="font-medium text-gray-700">{{ $helper['name'] }}</span>
                            <span class="font-bold text-gray-900">${{ number_format($helper['amount'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Task Table -->
            <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900">Spending by Task</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">Task</th>
                                <th class="px-6 py-3 font-medium">Helper</th>
                                <th class="px-6 py-3 font-medium text-right">Total Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($taskBreakdown as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $task['title'] }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $task['helper'] }}</td>
                                    <td class="px-6 py-4 font-mono text-gray-700 text-right">${{ number_format($task['fee'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Bar Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyData['labels']),
                    datasets: [{
                        label: 'Total Spent',
                        data: @json($monthlyData['values']),
                        backgroundColor: '#6366f1',
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
                            ticks: { callback: (val) => '$' + val }
                        }
                    }
                }
            });

            // Helper Doughnut Chart
            const helperCtx = document.getElementById('helperChart').getContext('2d');
            new Chart(helperCtx, {
                type: 'doughnut',
                data: {
                    labels: @json(array_column($helperData, 'name')),
                    datasets: [{
                        data: @json(array_column($helperData, 'amount')),
                        backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe', '#e0e7ff', '#f3f4f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>
</div>
