<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">My Applications</h1>
                <p class="text-gray-500 mt-1">Track the status of your job applications.</p>
            </div>
            <a href="{{ route('helpmate.dashboard') }}" class="text-sm font-bold text-green-600 hover:underline">
                &larr; Back to Dashboard
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Task Grid -->
        <div class="space-y-6">
            @if(empty($applications))
                <div class="text-center py-20 bg-white rounded-xl shadow-sm">
                    <p class="text-gray-500">You haven't applied to any tasks yet.</p>
                    <a href="{{ route('tasks.browse') }}" class="mt-4 inline-block text-green-600 font-bold">Browse Jobs</a>
                </div>
            @else
                @foreach($applications as $app)
                    <div
                        class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row gap-6 hover:shadow-lg transition-shadow duration-300">

                        <!-- Status Indicator (Corrected) -->
                        <div
                            class="w-full h-1 md:w-1 md:h-auto rounded-full {{ str_replace(['text-', 'border-'], 'bg-', $this->getStatusColorClass($app['status'])) }}">
                        </div>

                        <!-- Main Content -->
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-slate-900">{{ $app['title'] }}</h3>
                                <!-- Status Badge (Corrected) -->
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border {{ $this->getStatusColorClass($app['status']) }}">
                                    {{ str_replace('_', ' ', $app['status']) }}
                                </span>
                            </div>

                            <p class="text-sm text-emerald-700 font-semibold mb-3">Posted by {{ $app['user_name'] }}</p>
                            <p class="text-slate-500 text-sm leading-relaxed mb-4 line-clamp-2">{{ $app['description'] }}</p>

                            <div
                                class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500 border-t border-slate-100 pt-4">
                                <div class="flex items-center gap-1.5" title="Required Skill">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    {{ $app['skill'] }}
                                </div>
                                <div class="flex items-center gap-1.5" title="Rate">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    ${{ number_format($app['rate'], 2) }}/hr
                                </div>
                                <div class="flex items-center gap-1.5" title="Date Applied">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Applied on {{ $app['date'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex md:flex-col justify-end md:justify-start gap-2 pt-6 md:pt-0 md:border-l md:pl-6 border-t md:border-t-0 border-slate-100 md:w-44 flex-shrink-0">
                            <button wire:click="viewDetails({{ $app['id'] }})"
                                class="w-full bg-white border border-slate-300 text-slate-700 font-bold py-2 px-4 rounded-lg hover:bg-slate-50 transition text-sm">
                                View Details
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Details Modal -->
        @if($selectedApplication)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $selectedApplication['title'] }}</h3>

                    <div class="space-y-3">
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase">Posted By</span>
                            <p class="text-gray-800">{{ $selectedApplication['user_name'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase">Description</span>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $selectedApplication['description'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase">Location</span>
                            <p class="text-gray-800">{{ $selectedApplication['location'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-500 uppercase">Status</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="text-xs font-bold px-2 py-1 rounded-full uppercase {{ $this->getStatusColorClass($selectedApplication['status']) }}">
                                    {{ $selectedApplication['status'] }}
                                </span>
                                @if($selectedApplication['task_status'])
                                    <span class="text-xs text-gray-600">Task: {{ $selectedApplication['task_status'] }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Time Tracker for In-Progress Tasks -->
                        @if($selectedApplication['task_status'] === 'in_progress' && $selectedApplication['started_at'])
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4" x-data="{
                                            startTime: {{ \Carbon\Carbon::parse($selectedApplication['started_at'])->timestamp * 1000 }},
                                            elapsed: '00:00:00',
                                            rate: {{ $selectedApplication['rate'] }},
                                            earnings: '0.00'
                                        }" x-init="
                                            setInterval(() => {
                                                const diff = Date.now() - startTime;
                                                if (diff < 0) return;
                                                const totalSeconds = Math.floor(diff / 1000);
                                                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                                                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                                                const s = (totalSeconds % 60).toString().padStart(2, '0');
                                                elapsed = h + ':' + m + ':' + s;

                                                const hours = Math.max(0.5, Math.ceil((totalSeconds / 60) / 30) * 0.5);
                                                earnings = (hours * rate).toFixed(2);
                                            }, 1000)
                                        ">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-xs font-bold text-blue-600 uppercase mb-1">Time Elapsed</p>
                                        <p class="text-2xl font-mono font-bold text-blue-900" x-text="elapsed"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-bold text-green-600 uppercase mb-1">Estimated Earnings</p>
                                        <p class="text-2xl font-bold text-green-700">$<span x-text="earnings"></span></p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-100 mt-4">
                            <span class="font-bold text-emerald-700 text-lg">${{ number_format($selectedApplication['rate'], 2) }}/hr</span>
                            
                            {{-- Dynamically check payment status --}}
                            @if($selectedApplication['payment_status'] === 'paid')
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    Paid
                                </span>
                            @elseif($selectedApplication['payment_status'] === 'pending')
                                <span class="text-xs font-bold text-amber-600 bg-amber-100 px-3 py-1 rounded-full uppercase tracking-wider animate-pulse">
                                    Payment Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="closeModal"
                            class="px-4 py-2 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300 transition">
                            Close
                        </button>

                        @if($selectedApplication['status'] === 'accepted' && $selectedApplication['task_status'] === 'accepted')
                            <button wire:click="startTask({{ $selectedApplication['task_id'] }})"
                                class="px-4 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                                Start Task
                            </button>
                        @endif

                        @if($selectedApplication['task_status'] === 'in_progress')
                            <button wire:click="completeTask({{ $selectedApplication['task_id'] }})"
                                wire:confirm="Mark this task as completed?"
                                class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                                Complete Task
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>