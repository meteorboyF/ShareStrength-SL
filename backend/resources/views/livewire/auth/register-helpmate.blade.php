<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans py-12">
    <div class="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">

        <!-- Left Side: Visuals (Green Theme for HelpMates) -->
        <div class="w-full md:w-5/12 bg-green-600 text-white p-12 hidden md:flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -mt-10 -ml-10 blur-3xl"></div>
            
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="text-green-100 hover:text-white text-sm font-bold flex items-center gap-1 mb-8 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Home
                </a>
                <h2 class="text-4xl font-black tracking-tight mb-4">Join Our Team</h2>
                <p class="text-green-50 text-lg leading-relaxed">
                    Turn your compassion and skills into income. Become a trusted ShareStrength HelpMate today.
                </p>
            </div>

            <div class="space-y-4 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <span class="font-medium">Flexible Schedule</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
                    <span class="font-medium">Secure Payments</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <span class="font-medium">Verified Community</span>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-7/12 p-8 md:p-12">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Create HelpMate Account</h2>
                <div class="text-sm text-gray-500">
                    Not a helper? <a href="{{ route('register.user') }}" class="text-green-600 font-bold hover:underline">Register as User</a>
                </div>
            </div>

            <form wire:submit="register" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1.5">Full Name</label>
                        <input type="text" wire:model="name" required
                            class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="John Doe" />
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1.5">Email Address</label>
                        <input type="email" wire:model="email" required
                            class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="john@example.com" />
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Skills Selection (Interactive Pills) -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">What skills can you offer?</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableSkills as $skill)
                            <label class="cursor-pointer select-none group">
                                <input type="checkbox" value="{{ $skill }}" wire:model="skills" class="hidden peer">
                                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full border border-gray-200 bg-white text-gray-500 transition-all 
                                    peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 peer-checked:shadow-md peer-checked:shadow-green-100
                                    group-hover:border-green-300">
                                    {{ $skill }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('skills') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1.5">Password</label>
                        <input type="password" wire:model="password" required
                            class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="••••••••" />
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1.5">Confirm Password</label>
                        <input type="password" wire:model="password_confirmation" required
                            class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 focus:bg-white focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors" placeholder="••••••••" />
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-green-200 text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Register as HelpMate</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Creating Account...
                        </span>
                    </button>
                </div>
            </form>

            <p class="mt-8 text-center text-sm text-gray-500">
                Already have an account? <a href="{{ route('login') }}" class="text-green-600 font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</div>