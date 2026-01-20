<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans">
    <div
        class="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in-up">

        <!-- Left Side: Image and Quote -->
        <div
            class="w-full md:w-1/2 bg-purple-700 text-white p-12 hidden md:flex flex-col items-center justify-center text-center relative">
            <div class="absolute inset-0 bg-cover bg-center opacity-20"
                style="background-image: url('/img/indexbg.jpg')"></div>
            <div class="relative z-10">
                <a href="{{ route('home') }}"
                    class="flex items-center justify-center gap-2 mb-8 hover:opacity-80 transition block">
                    <img src="/img/logo2.png" alt="ShareStrength Logo" class="h-12 w-auto mx-auto"
                        style="filter: none;">
                </a>
                <h2 class="text-3xl font-bold mt-4 leading-tight">Your Independence, Supported.</h2>
                <p class="mt-4 text-purple-200 max-w-sm mx-auto">
                    Connecting individuals with disabilities to a community of vetted, skilled, and compassionate
                    helpers.
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <h2 class="text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    Sign in to your account
                </h2>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
                <form class="space-y-6" wire:submit="login">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email
                            address</label>
                        <div class="mt-2">
                            <input id="email" type="email" wire:model="email" autocomplete="email" required
                                class="block w-full rounded-lg border-0 p-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 bg-gray-50" />
                        </div>
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password"
                                class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="text-sm">
                                <a href="#" class="font-semibold text-purple-600 hover:text-purple-500">Forgot
                                    password?</a>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input id="password" type="password" wire:model="password" autocomplete="current-password"
                                required
                                class="block w-full rounded-lg border-0 p-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 bg-gray-50" />
                        </div>
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" wire:model="remember"
                            class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-lg bg-purple-600 px-3 py-3 text-base font-semibold leading-6 text-white shadow-lg hover:bg-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 transition-transform hover:-translate-y-0.5">
                            <span wire:loading.remove>Sign in</span>
                            <span wire:loading>Signing in...</span>
                        </button>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-500">
                    Not a member?
                    <a href="{{ route('register.user') }}"
                        class="font-semibold leading-6 text-purple-600 hover:text-purple-500">
                        Register as a User
                    </a>
                </p>
                <p class="mt-2 text-center text-sm text-gray-500">
                    Want to become a HelpMate?
                    <a href="{{ route('register.helpmate') }}"
                        class="font-semibold leading-6 text-green-600 hover:text-green-700">
                        Register as a HelpMate
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>