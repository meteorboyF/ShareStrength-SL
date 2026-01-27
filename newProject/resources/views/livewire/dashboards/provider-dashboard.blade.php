<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 p-6 bg-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Provider dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Signed in as {{ $provider?->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.provider') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">Profile</a>
                <button wire:click="logout" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Logout
                </button>
            </div>
        </div>
    </div>
</div>
