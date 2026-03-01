<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans py-12">
    <div class="w-full max-w-5xl mx-auto animate-fade-in-up">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-5">

            <!-- Left Side: Information & Tips (Purple Background) -->
            <div class="p-8 bg-purple-700 text-white md:col-span-2 flex flex-col justify-between">
                <div>
                    <a href="{{ route('dashboard') }}"
                        class="text-sm font-semibold text-purple-200 hover:text-white flex items-center gap-1 mb-8 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to Dashboard
                    </a>
                    <h1 class="text-3xl font-black tracking-tight">Post a New Task</h1>
                    <p class="mt-4 text-purple-200 text-sm leading-relaxed">Describe the support you need, and let our community of verified HelpMates find you.</p>

                    <div class="mt-10 space-y-6">
                        <div class="flex gap-4 items-start">
                            <div class="bg-purple-600 p-2 rounded-lg">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Be Specific</h4>
                                <p class="text-xs text-purple-200 mt-1">Clearly describe the task, including any specific requirements.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="bg-purple-600 p-2 rounded-lg">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Offer a Fair Rate</h4>
                                <p class="text-xs text-purple-200 mt-1">A competitive hourly rate will attract experienced HelpMates.</p>
                            </div>
                        </div>
                        <div class="flex gap-4 items-start">
                            <div class="bg-purple-600 p-2 rounded-lg">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Safety First</h4>
                                <p class="text-xs text-purple-200 mt-1">Remember, all HelpMates are verified by our platform for your peace of mind.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="p-8 md:col-span-3">
                <form wire:submit="postTask" class="space-y-6">
                    
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-800 mb-1.5">Task Title</label>
                        <input type="text" id="title" wire:model="title"
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 transition-colors"
                            placeholder="e.g. Help with grocery shopping" required />
                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-800 mb-1.5">Description</label>
                        <textarea id="description" wire:model="description" rows="4"
                            class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 transition-colors resize-none"
                            placeholder="Describe what you need help with, any specific times, locations, or requirements..." required></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- NEW: Map Location Selection -->
                    <div wire:ignore>
                        <label for="location" class="block text-sm font-bold text-gray-800 mb-1.5">Task Location</label>
                        <p class="text-xs text-gray-500 mb-2">Click on the map to pinpoint the location of the task.</p>
                        
                        <!-- The Map Container -->
                        <div id="map" class="h-56 w-full rounded-xl border border-gray-300 z-0 overflow-hidden shadow-inner"></div>
                        
                        <!-- Readonly address input that gets filled by JS -->
                        <input type="text" id="location" wire:model="location"
                            class="mt-3 block w-full rounded-xl border-gray-200 bg-gray-100 py-3 px-4 text-gray-700 shadow-sm focus:outline-none transition-colors"
                            placeholder="Drop a pin on the map to set address..." readonly required />
                        @error('location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Skills selection (Multi-select pills) -->
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1.5">Required Skills (Select one or more)</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableSkills as $skill)
                                <button type="button" 
                                    wire:click="toggleSkill('{{ $skill }}')" 
                                    class="px-4 py-2 rounded-full border text-sm font-semibold transition-all transform active:scale-95
                                    {{ in_array($skill, $selectedSkills) 
                                        ? 'bg-purple-600 text-white border-purple-600 shadow-md shadow-purple-200' 
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-purple-300 hover:bg-purple-50' }}">
                                    {{ $skill }}
                                </button>
                            @endforeach
                        </div>
                        @error('selectedSkills') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Urgency and Rate Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1.5">Urgency</label>
                            <div class="flex rounded-xl shadow-sm border border-gray-200 overflow-hidden p-1 bg-gray-50">
                                @foreach(['Low', 'Medium', 'High'] as $level)
                                    <button type="button" wire:click="$set('urgency', '{{ strtolower($level) }}')" 
                                        class="flex-1 px-4 py-2 text-sm font-bold rounded-lg transition-all duration-200
                                        {{ $urgency === strtolower($level) 
                                            ? 'bg-white text-purple-700 shadow-sm border border-gray-200' 
                                            : 'text-gray-500 hover:text-gray-700 border border-transparent' }}">
                                        {{ $level }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="budget" class="block text-sm font-bold text-gray-800 mb-1.5">Hourly Rate ($)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">$</span>
                                </div>
                                <input type="number" id="budget" wire:model="budget"
                                    class="block w-full rounded-xl border-gray-200 bg-gray-50 py-3 pl-8 pr-4 text-gray-900 shadow-sm focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-600/20 font-bold transition-colors"
                                    step="1" min="10" max="100" required />
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg shadow-purple-200 text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600 transition-all transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled" wire:target="postTask">
                            <span wire:loading.remove wire:target="postTask">Post Your Task</span>
                            <span wire:loading wire:target="postTask" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Posting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    
    // Make sure Leaflet is available before running
    if (typeof L === 'undefined') return;

    // Initialize map centered on Bangladesh
    const map = L.map('map').setView([23.8103, 90.4125], 7); 
    let marker;

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Fix map rendering issues inside flex/grid containers
    setTimeout(() => { map.invalidateSize(); }, 500);

    function updateLocation(latlng) {
        // Move or create marker
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng).addTo(map);
        }

        // Show a loading state in the input box
        document.getElementById('location').value = 'Fetching address...';

        // Reverse Geocoding (Turn coordinates into an address)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                const address = data.display_name || 'Address not found';
                
                // Update Livewire component variables safely
                @this.dispatch('locationSelected', {
                    address: address,
                    lat: latlng.lat,
                    lng: latlng.lng
                });
            })
            .catch(error => {
                console.error('Geocoding error:', error);
                document.getElementById('location').value = 'Error finding address. Please try again.';
            });
    }

    // Listen for clicks on the map
    map.on('click', function(e) {
        updateLocation(e.latlng);
    });

    // Optional: Ask browser for current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLatLng = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setView(userLatLng, 13);
            updateLocation(userLatLng);
        }, function(error) {
            // User denied location or it failed, just leave it at default center
            console.log("Geolocation not available or denied.");
        });
    }
});
</script>
@endpush