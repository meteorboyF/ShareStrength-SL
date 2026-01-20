<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans">
    <div
        class="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">

        <!-- Left Side: Visuals -->
        <div
            class="w-full md:w-1/2 bg-purple-700 text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-4">Find the Perfect HelpMate</h2>
                <p class="text-purple-200 text-lg">
                    Join our community to connect with vetted, compassionate HelpMates for your daily needs.
                </p>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Create User Account</h2>

            <form wire:submit="register" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Full Name</label>
                    <input type="text" wire:model="name" required
                        class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-purple-600 focus:border-purple-600" />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1">Email Address</label>
                    <input type="email" wire:model="email" required
                        class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-purple-600 focus:border-purple-600" />
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-1">Password</label>
                        <input type="password" wire:model="password" required
                            class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-purple-600 focus:border-purple-600" />
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-1">Confirm</label>
                        <input type="password" wire:model="password_confirmation" required
                            class="w-full rounded-lg border-gray-200 bg-gray-50 p-3 text-gray-900 focus:ring-2 focus:ring-purple-600 focus:border-purple-600" />
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-purple-700 text-white font-bold py-3 rounded-lg hover:bg-purple-800 transition shadow-md">
                    <span wire:loading.remove>Register</span>
                    <span wire:loading>Registering...</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Already have an account? <a href="{{ route('login') }}"
                    class="text-purple-600 font-bold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</div>