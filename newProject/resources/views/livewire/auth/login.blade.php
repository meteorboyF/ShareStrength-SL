<div class="mx-auto max-w-md space-y-6">
    <div class="rounded-xl border border-gray-200 p-6 bg-white">
        <h1 class="text-xl font-semibold">Sign in</h1>
        <p class="mt-1 text-sm text-gray-600">Choose your account type and login.</p>

        <form wire:submit="login" class="mt-6 space-y-4">
            <div>
                <label class="block text-sm font-medium">Account type</label>
                <select wire:model.live="accountType" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                    <option value="user">User</option>
                    <option value="provider">Provider</option>
                    <option value="admin">Admin</option>
                </select>
                @error('accountType') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
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

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300" />
                Remember me
            </label>

            <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white">
                Sign in
            </button>
        </form>
    </div>

    <div class="text-sm text-gray-600 space-y-1">
        <div>
            New user? <a class="text-gray-900 underline" href="{{ route('register.user') }}">Register user</a>
        </div>
        <div>
            New provider? <a class="text-gray-900 underline" href="{{ route('register.provider') }}">Register provider</a>
        </div>
        <div>
            Need an admin account? <a class="text-gray-900 underline" href="{{ route('register.admin') }}">Register admin</a>
        </div>
    </div>
</div>
