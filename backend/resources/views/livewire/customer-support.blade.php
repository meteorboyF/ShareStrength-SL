<div class="min-h-screen bg-slate-50 font-sans py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">Customer Support</h1>
            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
                Have a question or need assistance? We are here to help you navigate ShareStrength with ease.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Contact Info Cards (Left Side) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4">Contact Information</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Email Us</p>
                                <a href="mailto:support@sharestrength.test" class="text-sm text-indigo-600 hover:underline">support@sharestrength.test</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-purple-50 p-2 rounded-lg text-purple-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Call Us</p>
                                <p class="text-sm text-slate-500">+1 (555) 123-4567</p>
                                <p class="text-xs text-slate-400">Mon-Fri, 9am - 5pm EST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-teal-50 p-2 rounded-lg text-teal-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Office</p>
                                <p class="text-sm text-slate-500">123 Accessibility Lane,<br>Tech City, TC 90210</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Link -->
                <div class="bg-indigo-600 rounded-2xl shadow-lg p-6 text-white text-center">
                    <h3 class="font-bold text-lg mb-2">Check our FAQ</h3>
                    <p class="text-indigo-100 text-sm mb-4">Find answers to common questions about payments, tasks, and account settings.</p>
                    <button class="bg-white text-indigo-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-50 transition">
                        View Help Center
                    </button>
                </div>
            </div>

            <!-- Contact Form (Right Side) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    
                    @if($success)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center animate-fade-in-up">
                            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-xl font-bold text-green-800">Message Sent!</h3>
                            <p class="text-green-700 mt-2">Thank you for contacting us. Our support team will get back to you within 24 hours.</p>
                            <button wire:click="$set('success', false)" class="mt-6 text-green-800 font-bold hover:underline">Send another message</button>
                        </div>
                    @else
                        <h2 class="text-xl font-bold text-slate-800 mb-6">Send us a message</h2>
                        
                        <form wire:submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Your Name</label>
                                    <input type="text" wire:model="name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="John Doe">
                                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="john@example.com">
                                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                                <div class="relative">
                                    <select wire:model="subject" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none appearance-none transition">
                                        <option value="" disabled selected>Select a topic...</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Technical Issue">Technical Issue</option>
                                        <option value="Billing/Payments">Billing or Payments</option>
                                        <option value="Report User">Report a User</option>
                                        <option value="Feedback">Feedback / Suggestions</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                                @error('subject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Message</label>
                                <textarea wire:model="message" rows="5" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none transition" placeholder="Describe your issue or question..."></textarea>
                                @error('message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                                    <span wire:loading.remove>Send Message</span>
                                    <span wire:loading>Sending...</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>