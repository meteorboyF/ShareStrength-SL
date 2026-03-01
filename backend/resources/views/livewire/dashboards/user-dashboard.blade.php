<div class="min-h-screen bg-slate-50 font-sans text-slate-800 pb-12" wire:poll.3s="checkApprovals">    

<!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: User Profile -->
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-100" 
                             src="{{ $user->profile_photo_url ?? $user->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                             alt="{{ $user->name }}">
                        <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-green-400 border-2 border-white"></div>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-sm font-bold text-slate-900">Hello, {{ $user->name }}</div>
                        <div class="text-xs text-slate-500">Welcome back</div>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3">
                    <!-- Messages -->
                    <button @click="$dispatch('open-messages')" class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-all" title="Messages">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <!-- Cart -->
                    <a href="{{ route('cart') }}" class="relative p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-all" title="Marketplace Cart">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        @if($cartCount > 0)
                            <span class="absolute top-1 right-1 h-4 w-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <div class="h-6 w-px bg-slate-200 mx-1"></div>

                    <!-- Customer Support Button (Updated) -->
                    <!-- Used a dark slate color to differentiate it from the primary "Post Task" action in the sidebar -->
                    <a href="{{ route('support') }}" class="hidden md:flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-full text-sm font-bold shadow-md shadow-slate-200 transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Customer Support
                    </a>

                    <!-- Mobile Menu / Logout -->
                    <button wire:click="logout" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Logout">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Flash Message -->
        @if (session()->has('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-up">
                <div class="bg-emerald-100 p-1.5 rounded-full">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Banner -->
        @if($showBanner)
            <div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg p-6 sm:p-10 mb-8 overflow-hidden text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 animate-fade-in">
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl font-bold">Need assistive tools?</h2>
                    <p class="text-indigo-100 mt-1 max-w-lg">Visit the Marketplace to find high-quality devices and aids curated for your independence.</p>
                </div>
                <div class="relative z-10 flex items-center gap-4">
                    <a href="{{ route('marketplace') }}" class="bg-white text-indigo-600 font-bold px-6 py-3 rounded-xl shadow-sm hover:bg-indigo-50 transition-colors">
                        Browse Marketplace
                    </a>
                    <button wire:click="$set('showBanner', false)" class="text-white/60 hover:text-white transition-colors p-2">
                        <span class="sr-only">Dismiss</span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Main Content (8 cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Section 1: Review Applicants (High Priority) -->
                @if($tasksWithApplications->isNotEmpty())
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <h2 class="text-xl font-bold text-slate-800">Review Applications</h2>
                            <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-0.5 rounded-full">Action Needed</span>
                        </div>
                        <div class="space-y-4">
                            @foreach($tasksWithApplications as $task)
                                <div wire:key="app-task-{{ $task->id }}" class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden ring-1 ring-indigo-50">
                                    <button wire:click="toggleApplicants({{ $task->id }})" class="w-full flex items-center justify-between p-5 hover:bg-indigo-50/50 transition-colors text-left">
                                        <div>
                                            <h3 class="font-bold text-slate-900">{{ $task->title }}</h3>
                                            <p class="text-sm text-indigo-600 font-medium mt-0.5">{{ $task->applications->count() }} HelpMate(s) applied</p>
                                        </div>
                                        <div class="bg-indigo-100 p-2 rounded-full text-indigo-600">
                                            <svg class="w-5 h-5 transform transition-transform {{ $openApplicantTask === $task->id ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </button>

                                    @if($openApplicantTask === $task->id)
                                        <div class="bg-indigo-50/30 border-t border-indigo-100 p-4 space-y-3">
                                            @foreach($task->applications as $app)
                                                <div wire:key="app-{{ $app->id }}" class="bg-white p-4 rounded-xl border border-indigo-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $app->helper->profile_photo_url ?? $app->helper->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($app->helper->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                                             class="w-12 h-12 rounded-full object-cover border border-slate-200">
                                                        <div>
                                                            <a href="{{ route('profile.view.helpmate', ['id' => $app->helper_id]) }}" class="font-bold text-slate-900 hover:text-indigo-600 hover:underline">
                                                                {{ $app->helper->name }}
                                                            </a>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                <div class="flex text-yellow-400">
                                                                    @for($i=0; $i<5; $i++) <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endfor
                                                                </div>
                                                                <span class="text-xs text-slate-400">• New Applicant</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($app->status === 'pending')
                                                        <div class="flex items-center gap-2">
                                                            <button wire:click="messageHelper({{ $app->helper_id }}, {{ $task->id }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Message">
                                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                                            </button>
                                                            <button wire:click="rejectApplication({{ $app->id }})" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-colors">
                                                                Decline
                                                            </button>
                                                            <button wire:click="acceptApplication({{ $app->id }})" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 shadow-sm shadow-indigo-200 transition-colors">
                                                                Hire Helper
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-xs font-bold px-3 py-1 rounded-full uppercase
                                                            {{ $app->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                                            {{ $app->status }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

<!-- Section 2: Task List -->
                <section>
                    <div class="flex justify-between items-end mb-5">
                        {{-- The header now changes based on the toggle --}}
                        <h2 class="text-xl font-bold text-slate-800">
                            {{ $showCompleted ? 'Task History' : 'Your Active Tasks' }}
                        </h2>
                        
                        {{-- The label is now correct --}}
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <span class="text-sm font-medium text-slate-500 group-hover:text-slate-700">Show History</span>
                            <div class="relative">
                                <input type="checkbox" wire:model.live="showCompleted" class="sr-only peer">
                                <div class="w-10 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        @if($tasks->isEmpty())
                            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                                <div class="bg-slate-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">{{ $showCompleted ? 'No Completed Tasks' : 'No Active Tasks' }}</h3>
                                <p class="text-slate-500 mt-2 mb-6">{{ $showCompleted ? 'Your task history will appear here.' : 'You haven\'t posted any help requests yet.' }}</p>
                                @if(!$showCompleted)
                                    <a href="{{ route('tasks.post') }}" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition">
                                        Create First Task
                                    </a>
                                @endif
                            </div>
                        @else
                            @foreach($tasks as $task)
                                <div wire:key="task-{{ $task->id }}" class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider 
                                                        {{ $task->status === 'open' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                                        {{ $task->status === 'completed' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                                        {{ !in_array($task->status, ['open', 'in_progress', 'completed']) ? 'bg-slate-100 text-slate-600' : '' }}">
                                                        {{ str_replace('_', ' ', $task->status) }}
                                                    </span>
                                                    <span class="text-xs text-slate-400">{{ $task->created_at->diffForHumans() }}</span>
                                                </div>
                                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $task->title }}</h3>
                                            </div>
                                            
                                            <!-- Context Menu (Optional Actions) -->
                                            @if(in_array($task->status, ['open', 'in_progress']))
                                                <button wire:click="deleteTask({{ $task->id }})" wire:confirm="Are you sure?" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Delete Task">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            @endif
                                        </div>

                                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ $task->description }}</p>

                                        <div class="flex items-center justify-between border-t border-slate-50 pt-4 mt-auto">
                                            <div class="flex items-center gap-3">
                                                @if($task->caregiver)
                                                    <img src="{{ $task->caregiver->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($task->caregiver->name) }}" class="w-8 h-8 rounded-full border border-slate-200" title="Helper: {{ $task->caregiver->name }}">
                                                    <div class="text-xs">
                                                        <span class="block text-slate-400">Helper</span>
                                                        <span class="font-bold text-slate-700">{{ $task->caregiver->name }}</span>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                    </div>
                                                    <span class="text-xs text-slate-400 italic">Waiting for helper...</span>
                                                @endif
                                            </div>

                                            <!-- Live Timer for In-Progress -->
                                            @if($task->status === 'in_progress' && $task->started_at)
                                                <div class="bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 flex items-center gap-2"
                                                     x-data="{ 
                                                        startTime: {{ $task->started_at->timestamp * 1000 }}, 
                                                        elapsed: '00:00:00' 
                                                     }" 
                                                     x-init="setInterval(() => {
                                                            const diff = Date.now() - startTime;
                                                            if (diff < 0) return;
                                                            const totalSeconds = Math.floor(diff / 1000);
                                                            const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                                                            const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                                                            const s = (totalSeconds % 60).toString().padStart(2, '0');
                                                            elapsed = `${h}:${m}:${s}`;
                                                        }, 1000)">
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                                    <span class="font-mono text-sm font-bold text-blue-700" x-text="elapsed"></span>
                                                </div>
                                            @endif

                                            <!-- Actions for Completed/Cancelled -->
                                            @if(in_array($task->status, ['completed', 'cancelled']))
                                                <div class="flex gap-2">
                                                    <button wire:click="openRateModal({{ $task->id }}, '{{ addslashes($task->title) }}')" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xs font-bold rounded-lg transition-colors flex items-center gap-1">
                                                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        Rate
                                                    </button>
                                                    <button wire:click="repostTask({{ $task->id }})" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                                                        Repost
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right Column: Sidebar (4 cols) -->
            <aside class="lg:col-span-4 space-y-8">
                
                <!-- Quick Access Grid -->
                <section>
                    <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Quick Shortcuts</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('tasks.post') }}" class="col-span-2 bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-xl shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2 group">
                            <span class="font-bold">Post New Task</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </a>

                        @php
                            $shortcuts = [
                                ['label' => 'Contacts', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'trusted-contacts'],
                                ['label' => 'Profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'route' => 'my-profile'],
                                ['label' => 'Payment History', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'route' => 'payment-history'],
                                ['label' => 'Resources', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'route' => 'resources'],
                            ];
                        @endphp

                        @foreach($shortcuts as $link)
                            <a href="{{ route($link['route']) }}" class="bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 p-4 rounded-xl transition-all flex flex-col items-center justify-center gap-2 text-center group">
                                <div class="bg-slate-50 group-hover:bg-white p-2 rounded-full text-slate-500 group-hover:text-indigo-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}" /></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-600 group-hover:text-indigo-700">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <!-- Pending Payments -->
                @if($pendingPayments->isNotEmpty())
                    <section class="bg-white rounded-2xl border border-amber-100 shadow-sm overflow-hidden">
                        <div class="bg-amber-50 px-5 py-3 border-b border-amber-100">
                            <h2 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Payment Due
                            </h2>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @foreach($pendingPayments as $payment)
                                <div class="p-5">
                                    <div class="flex justify-between items-center mb-3">
                                        <div>
                                            <p class="font-bold text-slate-900 text-sm">{{ $payment->task->title ?? 'Service Payment' }}</p>
                                            <p class="text-xs text-slate-500">Helper: {{ $payment->payee->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-lg font-black text-slate-900">${{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('service-payment', ['paymentId' => $payment->id]) }}" class="block w-full bg-slate-900 hover:bg-black text-white text-center py-2 rounded-lg text-sm font-bold transition-colors shadow-lg shadow-slate-200">
                                        Pay Now
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            </aside>
        </div>
    </div>

<!-- Messages Slide-over (Refined) -->
    <div x-data="{ open: false }" @open-messages.window="open = true">
        <!-- Backdrop -->
        <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-50"></div>
        
        <!-- Sidebar Panel -->
        <div x-show="open" 
             x-transition:enter="transition transform duration-300 ease-out" 
             x-transition:enter-start="translate-x-full" 
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition transform duration-200 ease-in" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-80 sm:w-96 bg-white shadow-2xl z-50 flex flex-col border-l border-slate-100">
            
            <!-- Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="font-bold text-lg text-slate-800">Messages</h2>
                    <p class="text-xs text-slate-500">Your recent conversations</p>
                </div>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600 bg-white hover:bg-slate-100 p-2 rounded-full transition-colors border border-slate-200 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- List -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1 bg-white">
                @forelse($conversations as $conv)
                    <a href="{{ route('messages', ['conversationId' => $conv['id']]) }}" class="block p-3 hover:bg-indigo-50/60 rounded-xl transition-all group border border-transparent hover:border-indigo-100">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <img src="{{ $conv['other_user']['profile_photo_url'] ?? 'https://ui-avatars.com/api/?name='.urlencode($conv['other_user']['name']).'&color=7F9CF5&background=EBF4FF' }}" 
                                     class="w-12 h-12 rounded-full border border-slate-200 object-cover shadow-sm group-hover:border-indigo-200 transition-colors">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-0.5">
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 truncate transition-colors">{{ $conv['other_user']['name'] }}</h3>
                                    <span class="text-[10px] text-slate-400 group-hover:text-indigo-400 transition-colors">{{ $conv['last_message_at'] ? $conv['last_message_at']->diffForHumans(null, true) : '' }}</span>
                                </div>
                                <p class="text-xs text-slate-500 truncate group-hover:text-indigo-600/80 transition-colors font-medium">
                                    @if($conv['task']) 
                                        <span class="text-slate-400 group-hover:text-indigo-400">Ref:</span> {{ $conv['task']['title'] }} 
                                    @else 
                                        Direct Message 
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="h-full flex flex-col items-center justify-center p-10 text-center">
                        <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h3 class="text-slate-900 font-bold text-sm">No messages yet</h3>
                        <p class="text-slate-500 text-xs mt-1 max-w-[150px]">Connect with a HelpMate to start a conversation.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Footer Action -->
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                <a href="{{ route('messages') }}" class="block w-full bg-white border border-slate-200 text-slate-700 text-center py-3 rounded-xl text-sm font-bold hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm hover:shadow-md">
                    Open Full Chat Window
                </a>
            </div>
        </div>
    </div>

    <!-- Rating Modal (Refined) -->
    @if($showRateModal)
        <div class="fixed inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 ring-1 ring-black/5">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-8 text-center relative">
                    <h3 class="text-2xl font-black text-white tracking-tight">Rate Experience</h3>
                    <p class="text-indigo-100 text-sm mt-1 opacity-90">{{ $ratingTaskTitle }}</p>
                    <button wire:click="$set('showRateModal', false)" class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full p-1 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-8 text-center">
                    <p class="text-slate-600 font-medium mb-6">How was your helpmate?</p>

                    <div class="flex justify-center gap-2 mb-8">
                        @foreach(range(1, 5) as $star)
                            <button 
                                wire:click="setRating({{ $star }})" 
                                class="transition-transform hover:scale-110 focus:outline-none"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 {{ $ratingScore >= $star ? 'text-yellow-400 fill-current drop-shadow-sm' : 'text-slate-100' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endforeach
                    </div>

                    <textarea 
                        wire:model="ratingComment"
                        rows="3" 
                        placeholder="Optional feedback..." 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none mb-6"
                    ></textarea>

                    <button 
                        wire:click="submitReview"
                        class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
                        {{ $ratingScore === 0 ? 'disabled' : '' }}
                    >
                        Submit Review
                    </button>
                </div>
            </div>
        </div>
    @endif

<!-- Approval Modal (Handshake Protocol) -->
    @if($approvalTask)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md text-center transform scale-100 transition-all ring-1 ring-white/20">
                
                <!-- Icon -->
                <div class="mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-6 {{ $approvalTask->status === 'pending_start' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} animate-bounce">
                    @if($approvalTask->status === 'pending_start')
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @else
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </div>

                <h3 class="text-2xl font-black text-slate-900 mb-2">
                    {{ $approvalTask->status === 'pending_start' ? 'Ready to Start?' : 'Job Complete?' }}
                </h3>
                
                <p class="text-slate-600 mb-8 text-lg">
                    <span class="font-bold text-slate-800">{{ $approvalTask->caregiver->name }}</span> 
                    has requested to {{ $approvalTask->status === 'pending_start' ? 'begin working on' : 'mark as finished:' }}
                    <br>
                    <span class="italic text-slate-500">"{{ $approvalTask->title }}"</span>
                </p>

                <div class="flex flex-col gap-3">
                    @if($approvalTask->status === 'pending_start')
                        <button wire:click="approveStart" class="w-full py-3.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all hover:-translate-y-0.5">
                            Yes, Start Timer
                        </button>
                    @else
                        <button wire:click="approveEnd" class="w-full py-3.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                            Yes, Confirm Completion
                        </button>
                    @endif

                    <button wire:click="rejectRequest" class="w-full py-3.5 bg-white border border-slate-200 text-slate-500 font-bold rounded-xl hover:bg-slate-50 transition-colors">
                        Not right now
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>