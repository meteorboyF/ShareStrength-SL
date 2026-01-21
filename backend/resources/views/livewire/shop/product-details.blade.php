<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">

        <!-- Back Button -->
        <a href="{{ route('marketplace') }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 font-semibold text-sm transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Marketplace
        </a>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">

                <!-- Product Image -->
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                    <img
                        src="{{ $product->image_url ?: 'https://placehold.co/600x600?text=No+Image' }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                        onerror="this.src='https://placehold.co/600x600?text=No+Image'"
                    />
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <div class="flex-grow">
                        <p class="text-sm font-semibold text-purple-600 uppercase tracking-wide">{{ $product->vendor }}</p>
                        <h1 class="mt-2 text-3xl font-extrabold text-gray-900">{{ $product->name }}</h1>

                        <div class="mt-4 flex items-center gap-2">
                            <span class="text-3xl font-extrabold text-gray-900">${{ number_format($product->price, 2) }}</span>
                            @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                <span class="text-sm font-bold text-red-600">Only {{ $product->stock_quantity }} left!</span>
                            @elseif($product->stock_quantity == 0)
                                <span class="text-sm font-bold text-red-600">Out of Stock</span>
                            @endif
                        </div>

                        <p class="mt-6 text-gray-600 leading-relaxed">{{ $product->description }}</p>

                        <!-- Category Badge -->
                        @if($product->category)
                            <div class="mt-4">
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                    {{ ucfirst($product->category) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex items-center gap-4 mb-6">
                            <span class="text-sm font-semibold text-gray-700">Quantity:</span>
                            <div class="flex items-center gap-3">
                                <button
                                    wire:click="decrement"
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 font-bold text-gray-700 transition"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}
                                >
                                    -
                                </button>
                                <span class="font-bold text-lg w-8 text-center">{{ $quantity }}</span>
                                <button
                                    wire:click="increment"
                                    class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 font-bold text-gray-700 transition"
                                    {{ $quantity >= $product->stock_quantity ? 'disabled' : '' }}
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button
                                wire:click="addToCart"
                                class="flex-1 bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition shadow-lg disabled:bg-gray-400"
                                {{ $product->stock_quantity == 0 ? 'disabled' : '' }}
                            >
                                Add to Cart - ${{ number_format($product->price * $quantity, 2) }}
                            </button>
                        </div>

                        <a href="{{ route('cart') }}" class="block text-center mt-4 text-sm text-gray-500 hover:text-purple-600">
                            View Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
