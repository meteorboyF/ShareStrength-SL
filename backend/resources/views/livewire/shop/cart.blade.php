<div class="min-h-screen bg-gray-50 p-4 sm:p-8 font-sans">
    <div class="max-w-6xl mx-auto">

        <!-- Back to Marketplace -->
        <a href="{{ route('marketplace') }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 font-semibold text-sm transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Continue Shopping
        </a>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Shopping Cart</h1>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="bg-white rounded-xl p-12 text-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Your Cart is Empty</h2>
                <a href="{{ route('marketplace') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-purple-700 transition inline-block">
                    Browse Marketplace
                </a>
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="lg:w-2/3 space-y-4">
                    @foreach($cart as $productId => $item)
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                            <img
                                src="{{ $item['image_url'] ?: 'https://placehold.co/100x100?text=Item' }}"
                                alt="{{ $item['name'] }}"
                                class="w-20 h-20 object-cover rounded-lg bg-gray-50"
                                onerror="this.src='https://placehold.co/100x100?text=Item'"
                            />

                            <div class="flex-grow">
                                <h3 class="font-bold text-gray-900">{{ $item['name'] }}</h3>
                                <p class="text-sm text-gray-500">{{ $item['vendor'] ?? '' }}</p>
                                <p class="text-purple-600 font-bold mt-1">${{ number_format($item['price'], 2) }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <button
                                    wire:click="updateQuantity({{ $productId }}, -1)"
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 font-bold"
                                >-</button>
                                <span class="font-bold w-4 text-center">{{ $item['quantity'] }}</span>
                                <button
                                    wire:click="updateQuantity({{ $productId }}, 1)"
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 font-bold"
                                >+</button>
                            </div>

                            <button
                                wire:click="removeItem({{ $productId }})"
                                class="text-red-500 hover:text-red-700 p-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Summary / Checkout -->
                <div class="lg:w-1/3">
                    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 sticky top-8">
                        <h2 class="text-xl font-bold mb-4 border-b border-gray-100 pb-4">Order Summary</h2>

                        <div class="flex justify-between mb-2 text-gray-700">
                            <span>Subtotal</span>
                            <span>${{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between mb-4 text-gray-700">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>

                        <div class="flex justify-between mb-6 text-xl font-extrabold text-gray-900 border-t border-gray-100 pt-4">
                            <span>Total</span>
                            <span>${{ number_format($cartTotal, 2) }}</span>
                        </div>

                        <a
                            href="{{ route('checkout') }}"
                            class="block w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition shadow-md text-center"
                        >
                            Proceed to Checkout
                        </a>

                        <button
                            wire:click="clearCart"
                            wire:confirm="Are you sure you want to clear your cart?"
                            class="block w-full text-center mt-4 text-sm text-red-500 hover:text-red-700"
                        >
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
