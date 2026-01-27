<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 p-6 bg-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">User dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Signed in as {{ $user?->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.user') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">Profile</a>
                <button wire:click="logout" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 p-6 bg-gray-50">
        <div class="text-sm text-gray-700">
            Profile + chatbot + accessibility will live across all pages via the layout.
        </div>
    </div>
</div>
