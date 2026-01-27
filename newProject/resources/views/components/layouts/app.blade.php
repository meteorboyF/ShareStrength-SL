<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? 'newProject') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white text-gray-900">
        <div class="min-h-screen">
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="font-semibold tracking-tight">newProject</a>

                    <nav class="flex items-center gap-3 text-sm">
                        @php
                            $isUser = auth('web')->check();
                            $isProvider = auth('provider')->check();
                            $isAdmin = auth('admin')->check();
                        @endphp

                        @if (!$isUser && !$isProvider && !$isAdmin)
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                            <a href="{{ route('register.user') }}" class="text-gray-600 hover:text-gray-900">Register</a>
                        @else
                            @if ($isAdmin)
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                            @elseif ($isProvider)
                                <a href="{{ route('provider.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                                <a href="{{ route('profile.provider') }}" class="text-gray-600 hover:text-gray-900">Profile</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                                <a href="{{ route('profile.user') }}" class="text-gray-600 hover:text-gray-900">Profile</a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                            </form>
                        @endif
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 py-10">
                {{ $slot }}
            </main>
        </div>

        <x-accessibility-widget />
        <x-chatbot />

        @livewireScripts
    </body>
</html>
