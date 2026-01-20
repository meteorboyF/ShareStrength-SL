<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('helpmate.dashboard') }}" class="text-teal-600 hover:text-teal-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Browse Tasks</h1>
            </div>
            <button wire:click="logout" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">
                Log Out
            </button>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-6 lg:px-8">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3 animate-fade-in">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 animate-fade-in">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <input type="text" wire:model.live.debounce.300ms="searchTerm"
                        placeholder="Search tasks by title, description, or location..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-600 focus:border-transparent">
                </div>
                <div class="flex gap-2">
                    <select wire:model.live="filterUrgency"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-600 focus:border-transparent">
                        <option value="all">All Urgency</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tasks Grid -->
        @if($tasks->isEmpty())
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-500 text-lg">No tasks available.</p>
                <p class="text-sm text-gray-400 mt-2">Check back later for new opportunities!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tasks as $task)
                    <div wire:key="task-{{ $task->id }}"
                        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition border border-teal-100">
                        <div class="p-6">
                            <!-- Urgency Badge -->
                            <div class="flex items-start justify-between mb-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $task->urgency === 'high' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $task->urgency === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $task->urgency === 'low' ? 'bg-green-100 text-green-700' : '' }}
                                        ">
                                    {{ ucfirst($task->urgency) }} Priority
                                </span>
                                @if($task->applications->count() > 0)
                                    <span class="text-xs text-gray-500">
                                        {{ $task->applications->count() }} applicant(s)
                                    </span>
                                @endif
                            </div>

                            <!-- Task Details -->
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $task->title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $task->description }}</p>

                            <!-- Task Info -->
                            <div class="space-y-2 mb-4">
                                @if($task->location)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $task->location }}
                                    </div>
                                @endif

                                @if($task->budget)
                                    <div class="flex items-center gap-2 text-sm font-bold text-teal-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        ${{ number_format($task->budget, 2) }}
                                    </div>
                                @endif

                                @if($task->scheduled_at)
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($task->scheduled_at)->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Skills Required -->
                            @if($task->skills_required)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(json_decode($task->skills_required) as $skill)
                                        <span class="px-2 py-1 bg-teal-50 text-teal-700 rounded text-xs font-semibold">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Posted By -->
                            <div class="flex items-center gap-2 mb-4 pb-4 border-b border-gray-100">
                                <img src="{{ $task->creator->profile_photo ?? 'https://placehold.co/40x40' }}"
                                    alt="{{ $task->creator->name }}" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-xs text-gray-500">Posted by</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $task->creator->name }}</p>
                                </div>
                            </div>

                            <!-- Apply Button -->
                            @if(in_array($task->id, $userApplications))
                                <button disabled
                                    class="w-full bg-gray-300 text-gray-600 font-bold py-3 rounded-lg cursor-not-allowed">
                                    Already Applied
                                </button>
                            @else
                                <button wire:click="applyToTask({{ $task->id }})"
                                    class="w-full bg-teal-600 text-white font-bold py-3 rounded-lg hover:bg-teal-700 transition shadow-lg hover:shadow-xl">
                                    Apply Now
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>