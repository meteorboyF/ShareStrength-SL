<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 p-6 bg-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Admin dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Signed in as {{ $admin?->email }}</p>
            </div>
            <button wire:click="logout" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                Logout
            </button>
        </div>
    </div>
</div>
