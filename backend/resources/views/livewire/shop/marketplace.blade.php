<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header with Back, Search, and Cart -->
        <div class="flex items-center justify-between gap-4">
            <a href="{{ $isHelpmate ? route('helpmate.dashboard') : route('dashboard') }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 font-semibold text-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back to Dashboard
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search products..."
                        class="w-full px-4 py-2 pl-10 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Cart Icon -->
            <a href="{{ route('cart') }}" class="relative inline-flex items-center gap-2 text-gray-700 hover:text-purple-600 font-semibold transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php $cartCount = count(session('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Header -->
        <header class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Assistive Technology Marketplace</h1>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-500">Discover tools and technology designed to support independence and daily living.</p>
        </header>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="flex flex-wrap justify-center gap-3">
            <button
                wire:click="setCategory('all')"
                class="px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm border {{ $category === 'all' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}"
            >
                All
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="setCategory('{{ $cat }}')"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition shadow-sm border {{ $category === $cat ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}"
                >
                    {{ ucfirst($cat) }}
                </button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col overflow-hidden hover:-translate-y-2 hover:shadow-xl transition duration-300">
                    <!-- Image Container -->
                    <div class="w-full h-48 overflow-hidden bg-gray-100 relative">
                        <img
                            src="{{ $product->image_url ?: 'https://placehold.co/400x400?text=No+Image' }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover object-center"
                            onerror="this.src='https://placehold.co/400x400?text=No+Image'"
                        />
                    </div>

                    <!-- Content -->
                    <div class="p-4 flex flex-col flex-grow">
                        <div class="flex-grow">
                            <p class="text-xs font-semibold text-purple-600 uppercase">{{ $product->vendor }}</p>
                            <h3 class="mt-1 font-bold text-lg text-gray-900">{{ $product->name }}</h3>
                            <p class="mt-2 text-2xl font-extrabold text-gray-900">${{ number_format($product->price, 2) }}</p>

                            @if($product->stock_quantity <= 5)
                                <p class="text-xs font-bold text-red-600 mt-1">Only {{ $product->stock_quantity }} left in stock!</p>
                            @endif
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            <a
                                href="{{ route('product.details', $product->id) }}"
                                class="w-full text-center font-semibold text-sm bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition shadow-md"
                            >
                                View Details
                            </a>
                            <button
                                wire:click="addToCart({{ $product->id }})"
                                class="w-full text-center font-semibold text-sm bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition shadow-md"
                            >
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500 py-10">No products found.</p>
            @endforelse
        </div>

    </div>
</div>
