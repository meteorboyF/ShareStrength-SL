<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ $isHelpmate ? route('helpmate.dashboard') : route('dashboard') }}" class="text-purple-600 hover:text-purple-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1
                    class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                    Resource Library</h1>
            </div>
            <button wire:click="logout" class="text-sm font-semibold text-gray-800 hover:text-red-600 transition">
                Log Out
            </button>
        </div>
    </header>

    <div class="max-w-7xl mx-auto p-6 lg:px-8">
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

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-purple-100">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="searchTerm" placeholder="Search resources..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                </div>
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <button wire:click="setCategory('all')"
                        class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap transition {{ $selectedCategory === 'all' ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="setCategory({{ $category->id }})"
                            class="px-4 py-2 rounded-lg font-semibold whitespace-nowrap transition {{ (string) $selectedCategory === (string) $category->id ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- All Resources -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">
                    @if($searchTerm)
                        Search Results for "{{ $searchTerm }}"
                    @else
                        All Resources
                    @endif
                </h2>
            </div>

            @if($resources->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-purple-100">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-gray-500 text-lg">No resources found.</p>
                    <p class="text-sm text-gray-400 mt-2">Try adjusting your search or filter.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($resources as $resource)
                        <div wire:key="resource-{{ $resource->id }}"
                            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition border border-purple-100 group">
                            <!-- Resource Type Badge -->
                            <div class="h-2 bg-gradient-to-r from-purple-500 to-blue-500"></div>

                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <span
                                        class="px-3 py-1 bg-gradient-to-r from-purple-100 to-blue-100 text-purple-700 rounded-full text-xs font-bold">
                                        {{ $resource->category->name ?? 'General' }}
                                    </span>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-semibold uppercase">
                                        {{ $resource->type }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition">
                                    {{ $resource->title }}</h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $resource->description }}</p>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-xs text-gray-400">
                                        {{ $resource->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 flex gap-2">
                                    @if($resource->file_url)
                                        <a href="{{ $resource->file_url }}" target="_blank"
                                            class="flex-1 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition text-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                    @endif
                                    @if(!$isHelpmate)
                                        <button wire:click="requestAsTask({{ $resource->id }})"
                                            class="flex-1 inline-flex items-center justify-center gap-2 bg-white border-2 border-purple-600 text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition text-sm"
                                            title="Request help with this resource">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Request Help
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
