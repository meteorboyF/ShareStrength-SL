<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - ShareStrength</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
            <a href="{{ route('home') }}" class="inline-block">
                <img src="{{ asset('img/logo2.png') }}" alt="ShareStrength" class="h-10 mx-auto opacity-80 hover:opacity-100 transition">
            </a>
            <h2 class="mt-6 text-3xl font-black text-slate-900">Secure Checkout</h2>
            <p class="mt-2 text-sm text-slate-500">You are making a {{ $type === 'monthly' ? 'monthly' : 'one-time' }} donation.</p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-2xl sm:rounded-2xl sm:px-10 border border-slate-100">
                
                <!-- Order Summary -->
                <div class="bg-indigo-50 rounded-xl p-6 mb-8 text-center border border-indigo-100">
                    <p class="text-sm font-bold text-indigo-600 uppercase tracking-wider mb-1">Total Amount</p>
                    <p class="text-5xl font-black text-slate-900">${{ number_format($amount, 2) }}</p>
                </div>

                <form action="{{ route('donations.process') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Name on Card</label>
                        <div class="mt-1">
                            <input type="text" required placeholder="John Doe" class="appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Card Number</label>
                        <div class="mt-1 relative">
                            <input type="text" required placeholder="0000 0000 0000 0000" maxlength="19" class="appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors font-mono">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Expiry Date</label>
                            <input type="text" required placeholder="MM/YY" maxlength="5" class="mt-1 appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700">CVC</label>
                            <input type="text" required placeholder="123" maxlength="4" class="mt-1 appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors font-mono">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-200 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5 active:scale-95">
                            Pay ${{ number_format($amount, 2) }}
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-center gap-2 mt-4 text-xs text-slate-500 font-medium">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Guaranteed Safe & Secure Checkout
                    </div>
                </form>

            </div>
            
            <p class="text-center mt-6 text-sm text-slate-400 font-medium">
                <a href="{{ route('home') }}" class="hover:text-slate-600 transition">&larr; Cancel and return home</a>
            </p>
        </div>
    </div>

</body>
</html>