<div class="min-h-screen bg-slate-50 font-sans text-slate-900">

 {{-- Header --}}
<header class="bg-white shadow-sm sticky top-0 z-40 border-b border-green-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome, {{ $user->name }}!</h1>
            <p class="text-xs text-slate-500">Manage your jobs and find new tasks.</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- 1. Edit Profile Link (Now properly closed) --}}
            <a href="{{ route('my-profile') }}" class="hidden sm:inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                Edit Profile
            </a>

            {{-- 2. Message Icon Button --}}
            <button 
                @click="$dispatch('open-messages')" 
                type="button"
                class="relative p-2 text-slate-500 hover:text-green-600 hover:bg-green-50 rounded-full transition-all duration-200"
                title="Messages"
            >
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                </svg>
                <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </button>

            {{-- 3. Log Out Button --}}
            <button wire:click="logout" class="inline-flex items-center gap-x-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                Log Out
            </button>
        </div>
    </div>
</header>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- SIDEBAR --}}
            <aside class="lg:col-span-1 space-y-6">

                {{-- Profile Card --}}
                <section class="bg-green-700 text-white p-6 rounded-xl shadow-lg">
                    <div class="flex items-center gap-4">
                        <img class="h-16 w-16 rounded-full object-cover border-2 border-green-400"
                             src="{{ $user->profile_photo_url ?? $user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=059669&color=fff' }}"
                             alt="Profile">
                        <div>
                            <h3 class="font-bold text-lg">{{ $user->name }}</h3>
                            <p class="text-sm text-green-100">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="font-semibold text-green-200">Rating:</span>
                        <span class="font-bold text-yellow-300 flex items-center gap-1">
                            * {{ $user->rating ?? 'New' }}
                        </span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-600">
                        <h4 class="font-semibold text-sm text-green-200 mb-2">My Skills</h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($skills as $skill)
                                <span class="bg-green-600 text-white text-xs font-medium px-2 py-1 rounded-full border border-green-500">{{ $skill }}</span>
                            @empty
                                <span class="text-xs text-green-300">No skills listed</span>
                            @endforelse
                        </div>
                    </div>
                </section>

                {{-- Stats --}}
                <section class="bg-green-800 text-white p-6 rounded-xl shadow-lg">
                    <h3 class="font-semibold mb-2">My Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm text-green-200">Total Earnings</span>
                            <span class="text-2xl font-bold text-white">${{ number_format($totalEarnings, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="text-sm text-green-200">Completed Jobs</span>
                            <span class="text-2xl font-bold text-white">{{ $completedJobsCount }}</span>
                        </div>
                    </div>
                </section>

                {{-- Applied Jobs List --}}
                <section class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                    <h4 class="font-semibold text-sm mb-3 text-slate-600">Applied Jobs</h4>
                    <ul class="space-y-2">
                        @forelse($appliedJobs as $job)
                            <li class="text-sm p-2 bg-slate-50 rounded-md border border-slate-100">
                                <div class="font-medium text-slate-700">{{ $job['title'] }}</div>
                                <div class="text-xs text-slate-500">Posted by {{ $job['user_name'] }}</div>
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                                    @if($job['status'] === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($job['status'] === 'accepted') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($job['status']) }}
                                </span>
                            </li>
                        @empty
                            <li class="text-xs text-slate-400">No active applications.</li>
                        @endforelse
                    </ul>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                        <a href="{{ route('tasks.status') }}" class="text-xs font-bold text-green-600 hover:text-green-700 hover:underline">
                            View All Applications &rarr;
                        </a>
                    </div>
                </section>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="lg:col-span-2 space-y-6">

                {{-- Active Jobs (With Timer) --}}
                <section>
                    <h2 class="text-lg font-semibold text-slate-800 mb-3">Active Jobs</h2>
                    <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                        @if($activeJobs->isEmpty())
                            <p class="text-sm text-slate-400 text-center py-4">No jobs currently in progress.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach($activeJobs as $job)
                                    <li class="text-sm p-3 bg-blue-50 border border-blue-200 rounded-md">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                            <div>
                                                <p class="font-bold text-blue-800">{{ $job['title'] }}</p>
                                                <p class="text-xs text-blue-600">for {{ $job['user_name'] }} ({{ $job['status'] }})</p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                @if($job['user_id'])
                                                    <button
                                                        wire:click="messageUser({{ $job['user_id'] }}, {{ $job['id'] }})"
                                                        class="text-xs font-bold bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition"
                                                    >
                                                        Message
                                                    </button>
                                                @endif

                                                @if($job['status'] === 'accepted')
                                                    <button
                                                        wire:click="startTask({{ $job['id'] }})"
                                                        class="text-xs font-bold bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600 transition"
                                                    >
                                                        Start Task
                                                    </button>
                                                @endif

                                                @if($job['status'] === 'in_progress')
                                                    <div class="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm"
                                                         x-data="{
                                                             elapsed: '00:00:00',
                                                             startTime: {{ $job['started_at'] ? $job['started_at']->getTimestampMs() : 'null' }},
                                                             init() {
                                                                 if (!this.startTime) return;
                                                                 setInterval(() => {
                                                                     const diff = Date.now() - this.startTime;
                                                                     if (diff < 0) { this.elapsed = '00:00:00'; return; }
                                                                     const totalSeconds = Math.floor(diff / 1000);
                                                                     const hours = Math.floor(totalSeconds / 3600);
                                                                     const minutes = Math.floor((totalSeconds % 3600) / 60);
                                                                     const seconds = totalSeconds % 60;
                                                                     this.elapsed = hours.toString().padStart(2, '0') + ':' +
                                                                                    minutes.toString().padStart(2, '0') + ':' +
                                                                                    seconds.toString().padStart(2, '0');
                                                                 }, 1000);
                                                             }
                                                         }">
                                                        <span class="font-mono text-sm text-slate-700" x-text="elapsed"></span>
                                                    </div>
                                                    <button
                                                        wire:click="endTask({{ $job['id'] }})"
                                                        class="text-xs font-bold bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition"
                                                    >
                                                        End Task
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </section>

                {{-- Available Tasks Feed --}}
                <section>
                    <h2 class="text-lg font-semibold text-slate-800 mb-3">Available Tasks For You</h2>
                    <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        @forelse($availableTasks as $task)
                            <div class="bg-white p-5 rounded-lg border border-slate-200 shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex gap-3">
                                        <img src="{{ $task['user_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($task['user_name']) . '&background=6366f1&color=fff' }}"
                                             alt="{{ $task['user_name'] }}"
                                             class="h-10 w-10 rounded-full">
                                        <div>
                                            <h3 class="font-bold text-green-700">{{ $task['title'] }}</h3>
                                            <p class="text-xs text-slate-500">
                                                Posted by
                                                <a href="{{ route('profile.view.pwd', ['id' => $task['user_id']]) }}" class="hover:text-green-600 hover:underline">
                                                    {{ $task['user_name'] }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-slate-900">${{ number_format($task['hourly_rate'], 2) }}</p>
                                        <p class="text-xs text-slate-500">/hr</p>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-600 mt-3 mb-4">{{ $task['description'] }}</p>
                                <div class="flex justify-between items-center border-t border-slate-100 pt-4">
                                    <div class="flex gap-2">
                                        @if($task['skill'])
                                            <span class="bg-slate-100 text-slate-600 text-xs font-semibold px-2 py-1 rounded-full">{{ $task['skill'] }}</span>
                                        @endif
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full
                                            {{ $task['urgency'] === 'High' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $task['urgency'] }} Priority
                                        </span>
                                    </div>
                                    <button
                                        wire:click="openApplyModal({{ $task['id'] }}, '{{ addslashes($task['title']) }}')"
                                        class="text-sm font-semibold bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm"
                                    >
                                        Apply Now
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center bg-white p-12 rounded-lg border border-dashed border-slate-300">
                                <h3 class="mt-2 text-sm font-semibold text-slate-900">No Available Tasks</h3>
                                <p class="mt-1 text-sm text-slate-500">Check back later for new opportunities.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>
    </div>

    {{-- Apply Confirmation Modal --}}
    @if($showApplyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold text-slate-900">Confirm Application</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Do you want to apply for <strong class="text-green-700">{{ $selectedTaskTitle }}</strong>?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        wire:click="closeApplyModal"
                        class="px-4 py-2 text-sm font-semibold bg-slate-200 text-slate-800 rounded-md hover:bg-slate-300 transition"
                    >
                        Cancel
                    </button>
                    <button
                        wire:click="confirmApply"
                        class="px-4 py-2 text-sm font-semibold bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                    >
                        Yes, Apply Now
                    </button>
                </div>
            </div>
        </div>
    @endif
<div x-data="{ open: false }" @open-messages.window="open = true">
    <!-- Sidebar Overlay -->
    <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[60]"></div>

    <!-- Sidebar Panel -->
    <div x-show="open" 
         x-transition:enter="transition transform duration-300" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition transform duration-300" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-80 sm:w-96 bg-white shadow-2xl z-[70] flex flex-col">
        
        <!-- Header -->
        <div class="p-4 border-b flex items-center justify-between bg-green-600 text-white">
            <h2 class="font-bold text-lg">Recent Messages</h2>
            <button @click="open = false" class="hover:text-green-200">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Full Window Link -->
        <a href="{{ route('messages') }}" class="p-4 text-center text-sm font-bold text-green-700 border-b hover:bg-green-50 transition">
            Open Full Chat Window
        </a>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conv)
                <div class="p-4 border-b hover:bg-gray-50 cursor-pointer transition flex items-center gap-3">
                    <img src="{{ $conv['other_user']['profile_photo_url'] ?? 'https://placehold.co/50' }}" class="w-12 h-12 rounded-full border border-slate-200 shadow-sm">
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <h3 class="text-sm font-bold truncate text-slate-900">{{ $conv['other_user']['name'] }}</h3>
                            <span class="text-[10px] text-slate-400">{{ $conv['last_message_at'] ? $conv['last_message_at']->diffForHumans(null, true) : '' }}</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate font-medium">
                            @if($conv['task']) <span class="text-green-600">Job:</span> {{ $conv['task']['title'] }} @else Chat @endif
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    <p class="text-slate-400 text-sm italic">No recent messages.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>
