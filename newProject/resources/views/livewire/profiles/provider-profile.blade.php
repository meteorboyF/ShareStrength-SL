<div class="mx-auto max-w-xl space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h1 class="text-xl font-semibold">My Profile (Provider)</h1>

        @if (session('status'))
            <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="save" class="mt-6 space-y-4">
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
                <label class="block text-sm font-medium">About</label>
                <textarea wire:model="about" rows="5" class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm"></textarea>
                @error('about') <div class="mt-1 text-xs text-red-600">{{ $message }}</div> @enderror
            </div>

            <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white" type="submit">
                Save
            </button>
        </form>
    </div>
</div>
