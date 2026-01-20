<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans">
    <div
        class="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">

        <!-- Left Side: Visuals (Secondary/Green Theme) -->
        <div
            class="w-full md:w-1/2 bg-green-500 text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-4">Become a HelpMate</h2>
                <p class="text-green-50 text-lg">
                    Turn your compassion and skills into income. Join our team of trusted HelpMates today.
                </p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">HelpMate Registration</h2>

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Full Name</label>
                    <input type="text" wire:model="name" required
                        class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Email Address</label>
                    <input type="email" wire:model="email" required
                        class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Skills Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Your Skills</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($availableSkills as $skill)
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" value="{{ $skill }}" wire:model="skills" class="hidden peer">
                                <span
                                    class="px-3 py-1 text-xs font-bold rounded-full border border-gray-300 text-gray-500 bg-white transition hover:border-green-500 peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500">
                                    {{ $skill }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('skills') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-1">Password</label>
                        <input type="password" wire:model="password" required
                            class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-1">Confirm</label>
                        <input type="password" wire:model="password_confirmation" required
                            class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md">
                    <span wire:loading.remove>Register as HelpMate</span>
                    <span wire:loading>Registering...</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account? <a href="{{ route('login') }}"
                    class="text-green-600 font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</div>