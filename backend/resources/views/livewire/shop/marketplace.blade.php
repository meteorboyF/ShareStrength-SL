<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Header Navigation -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <a href="{{ $isHelpmate ? route('helpmate.dashboard') : route('dashboard') }}" 
               class="group inline-flex items-center gap-2 text-gray-500 hover:text-purple-600 font-bold text-sm transition-all">
                <div class="p-2 bg-white rounded-full shadow-sm group-hover:bg-purple-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                Back to Dashboard
            </a>

            <!-- Search Bar -->
            <div class="flex-1 max-w-md w-full">
                <div class="relative group">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search assistive technology..."
                        class="w-full px-5 py-3 pl-12 bg-white border border-gray-200 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Cart Icon (Top Right) -->
            <a href="{{ route('cart') }}" class="relative p-3 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md hover:border-purple-200 transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 group-hover:text-purple-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 bg-purple-600 text-white text-[10px] font-black rounded-full h-5 w-5 flex items-center justify-center border-2 border-white shadow-sm animate-bounce">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>

        <!-- Marketplace Title Section -->
        <header class="text-center py-4">
            <h1 class="text-4xl font-black tracking-tight text-gray-900 sm:text-5xl">Marketplace</h1>
            <p class="mt-4 max-w-xl mx-auto text-gray-500 leading-relaxed">High-quality assistive technology and tools curated to support your independence.</p>
        </header>

        <!-- Category Chips -->
        <div class="flex flex-wrap justify-center gap-2">
            <button
                wire:click="setCategory('all')"
                class="px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all {{ $category === 'all' ? 'bg-purple-600 text-white shadow-lg shadow-purple-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}"
            >
                All Items
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="setCategory('{{ $cat }}')"
                    class="px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all {{ $category === $cat ? 'bg-purple-600 text-white shadow-lg shadow-purple-200' : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}"
                >
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <!-- PRODUCT ADDED NOTIFICATION (Bottom Left Toast) -->
        @if (session()->has('added_to_cart'))
            @php $data = session('added_to_cart'); @endphp
            <div 
                x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 5000)" 
                x-show="show"
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-10 opacity-0 scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 translate-y-10 scale-95"
                @mouseenter="show = true" 
                class="fixed bottom-6 left-6 z-50 w-full max-w-sm bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5 flex flex-col"
            >
                <div class="p-4 flex gap-4 items-center border-b border-gray-50">
                    <!-- Product Image Thumbnail -->
                    <div class="h-16 w-16 flex-shrink-0 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden relative group">
                        <img src="{{ $data['image'] }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $data['name'] }}">
                    </div>

                    <!-- Text Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Success</span>
                            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 leading-tight truncate pr-2">{{ $data['name'] }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Added to your cart</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50/50 p-3 grid grid-cols-2 gap-3">
                    <button @click="show = false" class="w-full text-xs font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 py-2.5 rounded-lg transition-colors border border-gray-200 bg-white">
                        Keep Shopping
                    </button>
                    <a href="{{ route('cart') }}" class="w-full bg-purple-600 text-white text-xs font-bold py-2.5 rounded-lg text-center hover:bg-purple-700 shadow-md shadow-purple-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span>View Cart</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
                
                <!-- Animated Progress Bar -->
                <div class="h-1 bg-gray-100 w-full relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-purple-500 w-full animate-[shrink_5s_linear_forwards] origin-left"></div>
                </div>
                <style>
                    @keyframes shrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }
                </style>
            </div>
        @endif

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($products as $product)
                <div wire:key="product-{{ $product->id }}" class="group bg-white rounded-[2rem] border border-gray-100 shadow-sm flex flex-col overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                    
                    <!-- Image Container -->
                    <div class="relative w-full h-60 overflow-hidden bg-gray-50">
                        @php
                            // Determine Image Source (External URL vs Storage Path)
                            $imgSrc = 'https://placehold.co/600x400?text=No+Image';
                            if (!empty($product->image_url)) {
                                $imgSrc = str_starts_with($product->image_url, 'http') 
                                    ? $product->image_url 
                                    : asset('storage/' . $product->image_url);
                            }
                        @endphp
                        <img
                            src="{{ $imgSrc }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-110"
                            onerror="this.src='https://placehold.co/600x400?text=Image+Error'"
                        />
                        @if($product->price > 500)
                            <div class="absolute top-4 left-4 bg-black/80 backdrop-blur text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">
                                Premium
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-6 flex flex-col flex-grow relative">
                        <!-- Floating Cart Button (Optional, can be removed if prefer explicit button below) -->
                        <div class="absolute -top-6 right-6">
                            <button 
                                wire:click="addToCart({{ $product->id }})"
                                class="h-12 w-12 rounded-full bg-purple-600 text-white shadow-lg shadow-purple-200 hover:bg-purple-700 hover:scale-110 active:scale-95 transition-all flex items-center justify-center group/btn"
                                title="Quick Add"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transform group-hover/btn:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex-grow space-y-2 mt-2">
                            <div class="flex justify-between items-start">
                                <span class="text-[10px] font-black text-purple-600 uppercase tracking-widest bg-purple-50 px-2 py-1 rounded-md">{{ $product->vendor ?? 'ShareStrength' }}</span>
                                <div class="flex items-center text-yellow-400 gap-1 bg-yellow-50 px-2 py-1 rounded-md">
                                    <span class="text-[10px] font-bold text-yellow-600">5.0</span>
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                            </div>
                            
                            <h3 class="font-bold text-lg text-gray-900 leading-tight group-hover:text-purple-600 transition-colors line-clamp-2 min-h-[3.5rem]">
                                {{ $product->name }}
                            </h3>
                            
                            <div class="pt-2 flex items-baseline gap-2">
                                <p class="text-2xl font-black text-gray-900 tracking-tight">${{ number_format($product->price, 2) }}</p>
                                @if(isset($product->stock_quantity) && $product->stock_quantity <= 5)
                                    <span class="text-[10px] font-bold text-red-500 uppercase bg-red-50 px-2 py-0.5 rounded animate-pulse">Low Stock: {{ $product->stock_quantity }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Secondary Action Button -->
                        <div class="mt-6 pt-4 border-t border-gray-50">
                            <a
                                href="{{ route('product.details', $product->id) }}"
                                class="w-full flex items-center justify-center gap-2 text-center font-bold text-xs text-gray-500 hover:text-purple-600 hover:bg-purple-50 py-2.5 rounded-xl transition-colors uppercase tracking-widest"
                            >
                                View Details
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                    <div class="bg-gray-50 h-32 w-32 rounded-full flex items-center justify-center mb-6 shadow-inner">
                        <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">No products found</h3>
                    <p class="text-gray-500 mt-2 max-w-sm">We couldn't find anything matching your search. Try different keywords or clear the category filter.</p>
                    <button wire:click="$set('search', '')" class="mt-6 text-purple-600 font-bold hover:underline">Clear Search</button>
                </div>
            @endforelse
        </div>

    </div>
</div>