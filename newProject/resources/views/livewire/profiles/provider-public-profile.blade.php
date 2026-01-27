<div class="mx-auto max-w-2xl space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h1 class="text-xl font-semibold">{{ $provider->name }}</h1>
        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
            {{ $provider->profile['about'] ?? 'No public description yet.' }}
        </p>
    </div>
</div>
