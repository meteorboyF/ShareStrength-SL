<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Trusted Contacts</h1>
            </div>
            <button wire:click="logout" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">
                Log Out
            </button>
        </div>
    </header>

    <div class="max-w-5xl mx-auto p-6 lg:px-8">
        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3 animate-fade-in">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Add Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-purple-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Add New Contact</h2>
                <form wire:submit.prevent="addContact" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
                        <input type="text" wire:model="contact_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="John Doe">
                        @error('contact_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone *</label>
                        <input type="tel" wire:model="contact_phone"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="+1 234 567 8900">
                        @error('contact_phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" wire:model="contact_email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            placeholder="john@example.com">
                        @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Relation *</label>
                        <select wire:model="relation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            <option value="">Select Relation</option>
                            <option value="Family">Family</option>
                            <option value="Friend">Friend</option>
                            <option value="Neighbor">Neighbor</option>
                            <option value="Caregiver">Caregiver</option>
                            <option value="Healthcare Provider">Healthcare Provider</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('relation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-purple-600 text-white font-bold py-3 rounded-lg hover:bg-purple-700 transition shadow-lg hover:shadow-xl">
                        Add Contact
                    </button>
                </form>
            </div>

            <!-- Contacts List -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-purple-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Your Contacts</h2>
                <div class="space-y-4 max-h-[600px] overflow-y-auto">
                    @if($contacts->isEmpty())
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-gray-500">No trusted contacts yet.</p>
                            <p class="text-sm text-gray-400 mt-2">Add your first contact using the form.</p>
                        </div>
                    @else
                        @foreach($contacts as $contact)
                            <div wire:key="contact-{{ $contact->id }}"
                                class="bg-gradient-to-r from-purple-50 to-blue-50 p-4 rounded-lg border border-purple-200 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $contact->contact_name }}</h3>
                                        <p class="text-sm text-purple-600 font-semibold">{{ $contact->relation }}</p>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-700 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                {{ $contact->contact_phone }}
                                            </p>
                                            @if($contact->contact_email)
                                                <p class="text-sm text-gray-700 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $contact->contact_email }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="deleteContact({{ $contact->id }})"
                                        wire:confirm="Are you sure you want to delete this contact?"
                                        class="ml-4 text-red-500 hover:text-red-700 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>