<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}" class="flex justify-center mb-6">
            <img src="{{ asset('img/logo2.png') }}" alt="ShareStrength" class="h-10">
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create New Password</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            For {{ $email }}
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100">
            
            <form wire:submit.prevent="submit" class="space-y-6">
                @if($error)
                    <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg border border-red-100">{{ $error }}</div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" wire:model="password" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm mt-1 bg-gray-50">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm mt-1 bg-gray-50">
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition disabled:opacity-50">
                    <span wire:loading.remove>Save New Password</span>
                    <span wire:loading>Saving...</span>
                </button>
            </form>

        </div>
    </div>
</div>