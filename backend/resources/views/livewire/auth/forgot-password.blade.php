<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <a href="{{ route('home') }}" class="flex justify-center mb-6">
            <img src="{{ asset('img/logo2.png') }}" alt="ShareStrength" class="h-10">
        </a>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Reset your password</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Or <a href="{{ route('login') }}" class="font-medium text-purple-600 hover:text-purple-500">return to login</a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100">
            
            @if($resetLink)
                <!-- DEMO MODE SUCCESS: Shows the link directly -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-green-900 mb-2">Reset Link Generated!</h3>
                    <p class="text-sm text-green-700 mb-4">In a real app, this would be emailed to you. For this demo, click the button below to reset your password.</p>
                    <a href="{{ $resetLink }}" class="inline-block w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition shadow-md">
                        Go to Reset Password Page
                    </a>
                </div>
            @else
                <form wire:submit.prevent="submit" class="space-y-6">
                    @if($error)
                        <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg border border-red-100">{{ $error }}</div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Type</label>
                        <select wire:model="account_type" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm rounded-xl bg-gray-50 border">
                            <option value="user">User (PWD)</option>
                            <option value="helpmate">HelpMate</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email address</label>
                        <input type="email" wire:model="email" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm mt-1 bg-gray-50">
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition disabled:opacity-50">
                        <span wire:loading.remove>Send Password Reset Link</span>
                        <span wire:loading>Generating Link...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>