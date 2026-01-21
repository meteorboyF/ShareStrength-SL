<div class="min-h-screen bg-gray-100 font-sans p-4 sm:p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header / Cover -->
        <div class="h-32 bg-gradient-to-r from-purple-500 to-purple-700 relative">
            <a
                href="{{ url()->previous() }}"
                class="absolute top-4 left-4 bg-white/20 hover:bg-white/40 text-white p-2 rounded-full backdrop-blur-sm transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
        </div>

        <!-- Profile Info -->
        <div class="px-8 pb-8">
            <div class="relative flex justify-between items-end -mt-12 mb-6">
                <img
                    src="{{ $profile->profile_photo_url ?? $profile->profile_photo ?? 'https://placehold.co/150' }}"
                    alt="{{ $profile->name }}"
                    class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white shadow-md object-cover bg-white"
                />
                <div class="flex gap-3 mb-2">
                    @if($isOwnProfile)
                        <a
                            href="{{ route('my-profile') }}"
                            class="px-4 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow-sm transition"
                        >
                            Edit Profile
                        </a>
                    @else
                        <button
                            wire:click="startConversation"
                            class="px-4 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow-sm transition"
                        >
                            Message
                        </button>
                        @if($isHelper && $isPwdViewer)
                            <a
                                href="{{ route('tasks.post') }}"
                                class="px-4 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-sm transition"
                            >
                                Hire Now
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
                    {{ $profile->name }}
                    @if($isHelper && ($profile->is_verified ?? false))
                        <span class="text-blue-500" title="Verified HelpMate">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                            </svg>
                        </span>
                    @endif
                </h1>
                <p class="text-gray-500 font-medium flex items-center gap-1 mt-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $profile->location ?? 'Location not set' }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $profile->email }}
                    @if($profile->phone)
                        &bull; {{ $profile->phone }}
                    @endif
                </p>
            </div>

            <!-- Stats Row (for helpers) -->
            @if($isHelper)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 py-6 border-y border-gray-100">
                    <div>
                        <span class="block text-2xl font-bold text-gray-900">{{ $profile->rating ?? 0 }} ★</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Rating</span>
                    </div>
                    <div>
                        <span class="block text-2xl font-bold text-gray-900">{{ $profile->completed_jobs ?? 0 }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Jobs Done</span>
                    </div>
                    <div class="col-span-2 sm:col-span-2">
                        <span class="block text-sm font-bold text-gray-900 mb-1">Skills</span>
                        <div class="flex flex-wrap gap-1">
                            @if($profile->skills)
                                @foreach(explode(',', $profile->skills) as $skill)
                                    <span class="px-2 py-1 bg-purple-50 text-purple-700 text-xs font-semibold rounded-md border border-purple-100">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400">No skills listed</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Disability Type (for users) -->
            @if(!$isHelper && $profile->disability_type)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-sm text-blue-800"><strong>Disability Type:</strong> {{ $profile->disability_type }}</p>
                </div>
            @endif

            <!-- Bio -->
            <div class="mt-8">
                <h3 class="text-lg font-bold text-gray-900 mb-3">About</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $profile->bio ?? 'No bio provided yet.' }}
                </p>
            </div>

            <!-- Member Since -->
            <div class="mt-6 text-sm text-gray-500">
                Member since {{ $profile->created_at->format('F Y') }}
            </div>

        </div>
    </div>
</div>
