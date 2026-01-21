<div class="min-h-screen bg-gray-50 p-4 sm:p-8 font-sans">
    <div class="max-w-4xl mx-auto">

        <!-- Back to Cart -->
        <a href="{{ route('cart') }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 font-semibold text-sm transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Cart
        </a>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Checkout</h1>

        <!-- Error Message -->
        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Left: Order Details -->
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-900">Order Details</h2>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <ul class="divide-y divide-gray-100">
                        @foreach($cart as $item)
                            <li class="py-3 flex justify-between">
                                <div>
                                    <span class="font-bold text-gray-700">{{ $item['name'] }}</span>
                                    <span class="text-xs text-gray-500 block">Qty: {{ $item['quantity'] }}</span>
                                </div>
                                <span class="font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t border-gray-200 mt-4 pt-4 flex justify-between text-xl font-bold text-purple-600">
                        <span>Total To Pay</span>
                        <span>${{ number_format($cartTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Payment Form -->
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-900">Payment</h2>
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <form wire:submit="processPayment" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Shipping Address</label>
                            <textarea
                                wire:model="shippingAddress"
                                rows="2"
                                class="w-full p-3 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="123 Main St, City, Country"
                            ></textarea>
                            @error('shippingAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">Cardholder Name</label>
                            <input
                                type="text"
                                wire:model="cardholderName"
                                class="w-full p-3 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="John Doe"
                            />
                            @error('cardholderName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">Card Number</label>
                            <input
                                type="text"
                                wire:model="cardNumber"
                                class="w-full p-3 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="0000 0000 0000 0000"
                            />
                            @error('cardNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Expiry</label>
                                <input
                                    type="text"
                                    wire:model="expiry"
                                    class="w-full p-3 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="MM/YY"
                                />
                                @error('expiry') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">CVC</label>
                                <input
                                    type="text"
                                    wire:model="cvc"
                                    class="w-full p-3 border rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="123"
                                />
                                @error('cvc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-4 rounded-xl text-white font-bold shadow-md transition {{ $processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700' }}"
                            {{ $processing ? 'disabled' : '' }}
                        >
                            @if($processing)
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing Payment...
                                </span>
                            @else
                                Pay ${{ number_format($cartTotal, 2) }}
                            @endif
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
