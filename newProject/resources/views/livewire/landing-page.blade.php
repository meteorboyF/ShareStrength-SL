<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-6">
        <h1 class="text-2xl font-semibold">Starter Template</h1>
        <p class="mt-2 text-sm text-gray-600">
            This is a theme-neutral starter with multi-auth (user/provider/admin), accessibility, and an RAG chatbot.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="{{ route('login') }}" class="rounded-xl border border-gray-200 p-5 hover:border-gray-400">
            <div class="font-medium">Login</div>
            <div class="mt-1 text-sm text-gray-600">User / Provider / Admin</div>
        </a>
        <a href="{{ route('register.user') }}" class="rounded-xl border border-gray-200 p-5 hover:border-gray-400">
            <div class="font-medium">Register User</div>
            <div class="mt-1 text-sm text-gray-600">Create a user account</div>
        </a>
        <a href="{{ route('register.provider') }}" class="rounded-xl border border-gray-200 p-5 hover:border-gray-400">
            <div class="font-medium">Register Provider</div>
            <div class="mt-1 text-sm text-gray-600">Create a provider account</div>
        </a>
    </div>
</div>
