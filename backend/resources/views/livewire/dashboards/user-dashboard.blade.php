<div class="min-h-screen bg-gray-50 font-sans text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ $user->profile_photo_url ?? $user->profile_photo ?? 'https://placehold.co/100x100' }}" alt="Profile" class="h-12 w-12 rounded-full border-2 border-purple-600 object-cover">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Welcome, {{ $user->name }}!</h1>
                    <p class="text-xs text-gray-500">Your personal dashboard.</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('marketplace') }}" class="hidden sm:inline-flex items-center gap-x-2 rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50 border border-slate-200 transition">
                    Marketplace
                </a>

                <a href="{{ route('messages') }}" class="relative p-2 text-gray-800 hover:text-purple-600 transition" title="Messages">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </a>

                <a href="{{ route('cart') }}" class="relative p-2 text-gray-800 hover:text-purple-600 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    @if($cartCount > 0)
                        <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('tasks.post') }}" class="hidden sm:inline-flex bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-700 transition">
                    + Post New Task
                </a>
                <button wire:click="logout" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">Log Out</button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:px-8">

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Marketplace Banner -->
        @if($showBanner)
            <section class="bg-purple-600 rounded-xl shadow-lg mb-8 relative overflow-hidden text-white p-6 flex flex-col sm:flex-row items-center justify-between animate-fade-in-up">
                <div class="z-10">
                    <h2 class="text-xl font-bold">Explore the Marketplace</h2>
                    <p class="text-purple-200 text-sm mt-1">Find assistive devices and tools.</p>
                </div>
                <a href="{{ route('marketplace') }}" class="mt-4 sm:mt-0 bg-white text-purple-600 font-bold px-6 py-2 rounded-lg shadow hover:bg-gray-100 transition z-10">
                    Browse Products &rarr;
                </a>
                <button wire:click="$set('showBanner', false)" class="absolute top-2 right-2 text-white/50 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </section>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Column -->
            <main class="lg:col-span-2 space-y-8">

                <!-- 1. Open Tasks -->
                <section class="animate-fade-in-up">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Your Posted Tasks</h2>
                        <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model.live="showCompleted"
                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-600"
                            />
                            Show Completed
                        </label>
                    </div>
                    <div class="space-y-4">
                        @if($tasks->isEmpty())
                            <p class="text-gray-500">No tasks posted yet.</p>
                        @else
                            @foreach($tasks as $task)
                                <div wire:key="task-{{ $task->id }}" class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-blue-800">{{ $task->title }}</h3>
                                            <p class="text-sm text-gray-500">Status: {{ $task->status }}</p>
                                            @if($task->status === 'in_progress' && $task->caregiver)
                                                <p class="text-xs text-blue-600 mt-1">Helper: {{ $task->caregiver->name }}</p>
                                            @endif
                                            @if($task->status === 'accepted' && $task->caregiver)
                                                <p class="text-xs text-green-600 mt-1">Assigned to: {{ $task->caregiver->name }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-2">{{ $task->description }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="text-xs font-bold px-2 py-1 rounded-full 
                                                {{ $task->status === 'open' ? 'bg-green-100 text-green-600' : '' }}
                                                {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-600' : '' }}
                                                {{ $task->status === 'completed' ? 'bg-purple-100 text-purple-600' : '' }}
                                                {{ !in_array($task->status, ['open', 'in_progress', 'completed']) ? 'bg-gray-100 text-gray-600' : '' }}
                                            ">
                                                {{ strtoupper($task->status) }}
                                            </span>

                                            @if($task->status === 'in_progress' && $task->started_at)
                                                <div class="bg-blue-50 px-3 py-2 rounded-lg border border-blue-200" 
                                                     x-data="{ 
                                                        startTime: {{ $task->started_at->timestamp * 1000 }}, 
                                                        elapsed: '00:00:00' 
                                                     }" 
                                                     x-init="
                                                        setInterval(() => {
                                                            const diff = Date.now() - startTime;
                                                            if (diff < 0) return;
                                                            const totalSeconds = Math.floor(diff / 1000);
                                                            const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                                                            const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                                                            const s = (totalSeconds % 60).toString().padStart(2, '0');
                                                            elapsed = `${h}:${m}:${s}`;
                                                        }, 1000)
                                                     "
                                                >
                                                    <div class="text-xs text-blue-600 mb-1">Time Elapsed:</div>
                                                    <span class="font-mono text-sm text-blue-700 font-semibold" x-text="elapsed"></span>
                                                </div>
                                            @endif
                                            
                                            <div class="flex gap-2 flex-wrap">
                                                
                                                @if(in_array($task->status, ['completed', 'cancelled']))
                                                    <button wire:click="repostTask({{ $task->id }})" class="text-xs bg-green-50 text-green-600 px-3 py-1 rounded-full hover:bg-green-100 font-semibold">
                                                        🔄 Repost
                                                    </button>
                                                @endif
                                                @if(in_array($task->status, ['open', 'in_progress']))
                                                    <button 
                                                        wire:click="deleteTask({{ $task->id }})"
                                                        wire:confirm="Are you sure you want to delete this task?" 
                                                        class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded-full hover:bg-red-100 font-semibold"
                                                    >
                                                        🗑️ Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>

                <!-- 2. Review Applicants -->
                <section class="animate-fade-in-up delay-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Review Applicants</h2>
                    <div class="space-y-4">
                        @if($tasksWithApplications->isEmpty())
                            <p class="text-gray-500 text-sm">No new applicants to review.</p>
                        @else
                            @foreach($tasksWithApplications as $task)
                                <div wire:key="app-task-{{ $task->id }}" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                    <div
                                        wire:click="toggleApplicants({{ $task->id }})"
                                        class="p-5 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition"
                                    >
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $task->title }}</h3>
                                            <p class="text-sm text-purple-600">{{ $task->applications->count() }} HelpMate(s) applied</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform {{ $openApplicantTask === $task->id ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>

                                    @if($openApplicantTask === $task->id)
                                        <div class="border-t border-gray-100 bg-gray-50 p-4 space-y-3">
                                            @foreach($task->applications as $app)
                                                <div wire:key="app-{{ $app->id }}" class="flex justify-between items-center bg-white p-3 rounded-lg border border-gray-100">
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $app->helper->profile_photo_url ?? $app->helper->profile_photo ?? 'https://placehold.co/150' }}" alt="{{ $app->helper->name }}" class="w-10 h-10 rounded-full">
                                                        <div>
                                                            <a href="{{ route('profile.view.helpmate', ['id' => $app->helper_id]) }}" class="font-bold text-sm hover:text-purple-600 hover:underline">
                                                                {{ $app->helper->name }}
                                                            </a>
                                                            <p class="text-xs text-yellow-500">New</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 items-center">
                                                        <span class="text-xs font-bold uppercase 
                                                            {{ $app->status === 'accepted' ? 'text-green-600' : '' }}
                                                            {{ $app->status === 'rejected' ? 'text-red-600' : '' }}
                                                            {{ $app->status === 'pending' ? 'text-gray-400' : '' }}
                                                        ">
                                                            {{ $app->status }}
                                                        </span>
                                                        @if($app->status === 'pending')
                                                            <button
                                                                wire:click="rejectApplication({{ $app->id }})"
                                                                class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1.5 rounded-full hover:bg-red-100 transition"
                                                            >
                                                                Delete
                                                            </button>
                                                            <button
                                                                wire:click="acceptApplication({{ $app->id }})"
                                                                class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-green-600 transition"
                                                            >
                                                                Hire
                                                            </button>
                                                            <button
                                                                wire:click="messageHelper({{ $app->helper_id }}, {{ $task->id }})"
                                                                class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1.5 rounded-full hover:bg-blue-100 transition"
                                                            >
                                                                Message
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </main>

            <!-- Sidebar -->
            <aside class="space-y-8">
                <!-- Pending Payments -->
                <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm animate-fade-in-up delay-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Pending Payments</h2>
                    <ul class="space-y-4">
                        @if($pendingPayments->isEmpty())
                            <p class="text-gray-500 text-sm">No pending payments.</p>
                        @else
                            @foreach($pendingPayments as $payment)
                                <li class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <p class="font-medium text-sm text-gray-900">{{ $payment->task->title ?? 'Service Payment' }}</p>
                                            <p class="text-xs text-gray-500">w/ {{ $payment->payee->name ?? 'Helper' }}</p>
                                        </div>
                                        <span class="font-bold text-gray-900">${{ number_format($payment->amount, 2) }}</span>
                                    </div>
                                    <a
                                        href="{{ route('service-payment', ['paymentId' => $payment->id]) }}"
                                        class="block w-full text-center bg-green-50 text-green-700 text-xs font-bold py-2 rounded-lg hover:bg-green-100 transition"
                                    >
                                        Confirm & Pay
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </section>

                <!-- Quick Access -->
                <section class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm animate-fade-in-up delay-300">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Quick Access</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('trusted-contacts') }}" class="col-span-2 p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            Manage Trusted Contacts
                        </a>
                        <a href="{{ route('my-profile') }}" class="p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            My Profile
                        </a>
                        <a href="{{ route('payment-history') }}" class="p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            Payment History
                        </a>
                        <a href="{{ route('resources') }}" class="p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            Resources
                        </a>
                        <a href="{{ route('messages') }}" class="p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            Messages
                        </a>
                        <a href="{{ route('marketplace') }}" class="col-span-2 p-3 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-purple-600/50 transition text-center flex items-center justify-center text-gray-800 cursor-pointer">
                            Marketplace
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>
