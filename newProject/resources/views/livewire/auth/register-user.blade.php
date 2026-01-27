<div class="mx-auto max-w-md space-y-6">
    <div class="rounded-xl border border-gray-200 p-6 bg-white">
        <h1 class="text-xl font-semibold">Register (User)</h1>

        <form wire:submit="register" class="mt-6 space-y-4">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
                @error('name') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" wire:model="email" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
                @error('email') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" wire:model="password" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
                @error('password') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Confirm password</label>
                <input type="password" wire:model="password_confirmation" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
            </div>

            <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white">
                Create account
            </button>
        </form>
    </div>

    <div class="text-sm text-gray-600">
        Already have an account? <a class="text-gray-900 underline" href="{{ route('login') }}">Login</a>
    </div>
</div>
