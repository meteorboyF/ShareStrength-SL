<div class="min-h-screen bg-gray-50 font-sans p-4 sm:p-8 flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="bg-purple-600 p-6 text-white text-center">
            <h1 class="text-2xl font-bold">Secure Service Payment</h1>
            <p class="text-purple-200 text-sm mt-1">Completion of Task: #{{ $task->id }}</p>
        </div>

        <div class="p-8">
            <!-- Flash Messages -->
            @if (session()->has('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Helpmate Info -->
            <div class="flex items-center gap-4 mb-8 pb-8 border-b border-gray-100">
                <img
                    src="{{ $helper->profile_photo_url ?? $helper->profile_photo ?? 'https://placehold.co/150' }}"
                    alt="{{ $helper->name }}"
                    class="w-16 h-16 rounded-full border-2 border-gray-100 object-cover"
                />
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Payment to {{ $helper->name }}</h2>
                    <p class="text-gray-500 text-sm">For {{ $task->title }}</p>
                </div>
            </div>

            <!-- Bill Breakdown -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-100">
                <div class="flex justify-between mb-3 text-sm">
                    <span class="text-gray-600">Hourly Rate</span>
                    <span class="font-semibold">${{ number_format($task->budget, 2) }}/hr</span>
                </div>
                @if($task->started_at && $task->completed_at)
                    @php
                        $hours = $task->started_at->diffInMinutes($task->completed_at) / 60;
                        $hours = max(0.5, ceil($hours * 2) / 2);
                    @endphp
                    <div class="flex justify-between mb-3 text-sm">
                        <span class="text-gray-600">Hours Logged</span>
                        <span class="font-semibold">{{ $hours }} hrs</span>
                    </div>
                @endif
                <div class="flex justify-between mb-3 text-sm border-b border-gray-200 pb-3">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-600">Platform Fee (10%)</span>
                    <span class="font-semibold">${{ number_format($platformFee, 2) }}</span>
                </div>
                <div class="flex justify-between mt-4 text-xl font-extrabold text-gray-900">
                    <span>Total Due</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Select Payment Method</label>
                <div class="flex gap-3">
                    <button type="button" class="flex-1 py-3 border-2 border-purple-600 bg-purple-50 text-purple-600 font-bold rounded-lg text-sm">
                        Saved Card **** 4242
                    </button>
                    <button type="button" class="flex-1 py-3 border border-gray-200 text-gray-500 font-bold rounded-lg text-sm hover:bg-gray-50">
                        New Card
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <a href="{{ route('dashboard') }}" class="flex-1 py-4 text-center text-gray-500 font-bold hover:text-gray-800 transition">
                    Cancel
                </a>
                <button
                    wire:click="processPayment"
                    class="flex-[2] py-4 rounded-xl text-white font-bold shadow-lg transition transform active:scale-95 {{ $processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}"
                    {{ $processing ? 'disabled' : '' }}
                >
                    @if($processing)
                        <span class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    @else
                        Confirm & Pay ${{ number_format($total, 2) }}
                    @endif
                </button>
            </div>
        </div>

    </div>
</div>
