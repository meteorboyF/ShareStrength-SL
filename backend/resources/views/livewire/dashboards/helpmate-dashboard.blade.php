<div class="min-h-screen bg-slate-50 font-sans text-slate-800 pb-12">

    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-100 p-2 rounded-lg text-emerald-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">HelpMate Console</h1>
                    <p class="text-xs text-slate-500 font-medium">Ready to help, {{ explode(' ', $user->name)[0] }}?</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('my-profile') }}" class="hidden sm:flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profile
                </a>

                <button 
                    @click="$dispatch('open-messages')" 
                    class="relative p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-full transition-all"
                    title="Messages"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    <span class="absolute top-1.5 right-1.5 h-2.5 w-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                </button>

                <div class="h-6 w-px bg-slate-200 mx-1"></div>

                <button wire:click="logout" class="text-sm font-semibold text-slate-500 hover:text-red-600 px-2 py-2 rounded-lg transition-colors">
                    Log Out
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- Alerts --}}
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3 text-emerald-800 text-sm font-medium animate-fade-in-up shadow-sm">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- SIDEBAR (Left - 4 Cols) --}}
            <aside class="lg:col-span-4 space-y-6">

                {{-- Profile Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative group">
                    <div class="h-24 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
                    <div class="px-6 pb-6 relative">
                        <div class="flex justify-between items-end -mt-10 mb-4">
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-md"
                                 src="{{ $user->profile_photo_url ?? $user->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=10b981&color=fff' }}"
                                 alt="Profile">
                            <div class="text-right mb-1">
                                <div class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 px-2 py-1 rounded-lg border border-yellow-100 text-xs font-bold">
                                    <svg class="w-3 h-3 text-yellow-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $user->rating ?? '5.0' }}
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                            <p class="text-sm text-slate-500">{{ $user->email }}</p>
                        </div>

                        <div class="mt-5">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">My Expertise</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($skills as $skill)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $skill }}
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">No skills added yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Earnings Stats --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center items-center text-center">
                        <div class="bg-emerald-100 p-2 rounded-full text-emerald-600 mb-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Total Earned</p>
                        <p class="text-xl font-black text-slate-900 tracking-tight">${{ number_format($totalEarnings, 2) }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-center items-center text-center">
                        <div class="bg-indigo-100 p-2 rounded-full text-indigo-600 mb-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Jobs Done</p>
                        <p class="text-xl font-black text-slate-900 tracking-tight">{{ $completedJobsCount }}</p>
                    </div>
                </div>

                {{-- Application Tracker --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800">My Applications</h3>
                        <a href="{{ route('tasks.status') }}" class="text-xs font-bold text-emerald-600 hover:underline">View All</a>
                    </div>
                    <ul class="space-y-3">
                        @forelse($appliedJobs as $job)
                            <li class="flex justify-between items-center text-sm p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="flex-1 min-w-0 pr-3">
                                    <p class="font-semibold text-slate-900 truncate">{{ $job['title'] }}</p>
                                    <p class="text-xs text-slate-500">for {{ $job['user_name'] }}</p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full
                                    @if($job['status'] === 'pending') bg-amber-100 text-amber-700
                                    @elseif($job['status'] === 'accepted') bg-emerald-100 text-emerald-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $job['status'] }}
                                </span>
                            </li>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-sm italic">
                                You haven't applied to any jobs yet.
                            </div>
                        @endforelse
                    </ul>
                </div>

            </aside>

            {{-- MAIN CONTENT (Right - 8 Cols) --}}
            <main class="lg:col-span-8 space-y-8">

                {{-- ACTIVE JOBS SECTION (Priority) --}}
                <section>
                    <div class="flex items-center gap-2 mb-4">
                        <h2 class="text-lg font-bold text-slate-800">Current Assignments</h2>
                        @if($activeJobs->count() > 0)
                            <span class="bg-emerald-600 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $activeJobs->count() }} Active</span>
                        @endif
                    </div>

                    @if($activeJobs->isEmpty())
                        <div class="bg-white border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center">
                            <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <p class="text-slate-500 font-medium">You have no active jobs right now.</p>
                            <p class="text-xs text-slate-400 mt-1">Browse available tasks below to get started.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($activeJobs as $job)
                                <div class="bg-white rounded-2xl shadow-md border-l-4 {{ $job['status'] === 'in_progress' ? 'border-blue-500' : 'border-emerald-500' }} overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded text-white {{ $job['status'] === 'in_progress' ? 'bg-blue-500' : 'bg-emerald-500' }}">
                                                        {{ str_replace('_', ' ', $job['status']) }}
                                                    </span>
                                                    <span class="text-xs text-slate-400 font-medium">Order #{{ $job['id'] }}</span>
                                                </div>
                                                <h3 class="text-xl font-bold text-slate-900">{{ $job['title'] }}</h3>
                                                <p class="text-sm text-slate-600 mt-1">Client: <span class="font-semibold">{{ $job['user_name'] }}</span></p>
                                            </div>

                                            {{-- Timer Display --}}
                                            @if($job['status'] === 'in_progress')
                                                <div class="bg-blue-50 border border-blue-100 px-4 py-2 rounded-xl text-center min-w-[120px]"
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
                                                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wide">Time Elapsed</p>
                                                    <p class="text-xl font-mono font-bold text-blue-700" x-text="elapsed">00:00:00</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-6 flex flex-wrap gap-3 pt-4 border-t border-slate-100">
                                            @if($job['user_id'])
                                                <button wire:click="messageUser({{ $job['user_id'] }}, {{ $job['id'] }})" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                                                    Message Client
                                                </button>
                                            @endif

                                            @if($job['status'] === 'accepted')
                                                <button wire:click="startTask({{ $job['id'] }})" class="flex items-center gap-2 px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 shadow-md shadow-emerald-200 transition">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Start Work
                                                </button>
                                            @endif

                                            @if($job['status'] === 'in_progress')
                                                <button wire:click="endTask({{ $job['id'] }})" class="flex items-center gap-2 px-6 py-2 bg-red-500 text-white rounded-lg text-sm font-bold hover:bg-red-600 shadow-md shadow-red-200 transition ml-auto">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Complete Job
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>


 {{-- AVAILABLE TASKS FEED --}}
                <section>
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                        <h2 class="text-lg font-bold text-slate-800">New Opportunities</h2>
                        
                        <!-- THE TOGGLE IS BACK! -->
                        <label class="flex items-center gap-2 cursor-pointer group" title="Toggle to show all jobs, not just those matching your skills.">
                            <span class="text-sm font-medium text-slate-500 group-hover:text-emerald-700 transition-colors">
                                Show only relevant jobs
                            </span>
                            <div class="relative">
                                <input type="checkbox" wire:model.live="showRelevantOnly" class="sr-only peer">
                                <div class="w-10 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        @forelse($availableTasks as $task)
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all group">
                                <div class="flex justify-between items-start">
                                    <div class="flex gap-4">
                                        <img src="{{ $task['user_photo'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($task['user_name']) . '&background=6366f1&color=fff' }}"
                                             alt="{{ $task['user_name'] }}"
                                             class="h-12 w-12 rounded-full object-cover border border-slate-100">
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">{{ $task['title'] }}</h3>
                                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                                <span>Posted by {{ $task['user_name'] }}</span>
                                                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                    {{ $task['location'] ?? 'Remote' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-black text-emerald-600">${{ number_format($task['hourly_rate'], 2) }}</p>
                                        <p class="text-xs font-bold text-slate-400 uppercase">Fixed</p>
                                    </div>
                                </div>

                                <p class="text-sm text-slate-600 mt-4 leading-relaxed line-clamp-2">{{ $task['description'] }}</p>

                                <div class="flex justify-between items-center border-t border-slate-50 pt-4 mt-4">
                                    <div class="flex flex-wrap gap-2">
                                        @if($task['skill'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                                {{ $task['skill'] }}
                                            </span>
                                        @endif
                                        @if($task['urgency'] === 'High')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                                High Priority
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <button
                                        wire:click="openApplyModal({{ $task['id'] }}, '{{ addslashes($task['title']) }}')"
                                        class="px-5 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-emerald-600 hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2"
                                    >
                                        <span>Apply Now</span>
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center p-12 bg-white rounded-2xl border border-dashed border-slate-300 text-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-4">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">All caught up!</h3>
                                @if($showRelevantOnly)
                                    <p class="text-slate-500 mt-1 max-w-xs">There are no new tasks that match your skills. Try toggling the filter off to see all available opportunities.</p>
                                @else
                                    <p class="text-slate-500 mt-1 max-w-xs">There are no new tasks available right now. Check back soon for more opportunities.</p>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>
    </div>

    {{-- Apply Confirmation Modal --}}
    @if($showApplyModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md transform transition-all scale-100 ring-1 ring-black/5">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 mb-4">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900">Confirm Application</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        You are about to apply for <br><strong class="text-emerald-700">{{ $selectedTaskTitle }}</strong>.
                    </p>
                </div>
                
                <div class="flex flex-col gap-3">
                    <button
                        wire:click="confirmApply"
                        class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-md shadow-emerald-200 transition-all hover:-translate-y-0.5"
                    >
                        Yes, Apply Now
                    </button>
                    <button
                        wire:click="closeApplyModal"
                        class="w-full py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Messages Sidebar (Updated) --}}
    <div x-data="{ open: false }" @open-messages.window="open = true">
        <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[60]"></div>
        <div x-show="open" 
             x-transition:enter="transition transform duration-300" 
             x-transition:enter-start="translate-x-full" 
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition transform duration-300" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-80 sm:w-96 bg-white shadow-2xl z-[70] flex flex-col border-l border-slate-100">
            
            <div class="p-5 border-b border-emerald-100 flex items-center justify-between bg-emerald-50">
                <h2 class="font-bold text-lg text-emerald-900">Inbox</h2>
                <button @click="open = false" class="text-emerald-400 hover:text-emerald-700 bg-white p-2 rounded-full shadow-sm hover:shadow transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @forelse($conversations as $conv)
                    <a href="{{ route('messages', ['conversationId' => $conv['id']]) }}" class="block p-3 hover:bg-emerald-50 rounded-xl transition-colors group">
                        <div class="flex items-center gap-3">
                            <img src="{{ $conv['other_user']['profile_photo_url'] ?? 'https://placehold.co/50' }}" class="w-10 h-10 rounded-full border border-slate-200">
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline">
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 truncate">{{ $conv['other_user']['name'] }}</h3>
                                    <span class="text-[10px] text-slate-400">{{ $conv['last_message_at'] ? $conv['last_message_at']->diffForHumans(null, true) : '' }}</span>
                                </div>
                                <p class="text-xs text-slate-500 truncate group-hover:text-emerald-600">
                                    @if($conv['task']) <span class="font-semibold text-slate-400">Job:</span> {{ $conv['task']['title'] }} @else Direct Message @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center">
                        <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        </div>
                        <p class="text-slate-400 text-sm">No new messages.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                <a href="{{ route('messages') }}" class="block w-full bg-white border border-slate-200 text-slate-700 text-center py-2.5 rounded-xl text-sm font-bold hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition-all shadow-sm">
                    Open Full Chat
                </a>
            </div>
        </div>
    </div>

</div>