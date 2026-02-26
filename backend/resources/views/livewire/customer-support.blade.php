<div class="min-h-screen bg-slate-50 font-sans py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">Customer Support</h1>
            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
                Need assistance? Open a support ticket and our team will get back to you as soon as possible.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Contact Info Cards (Left Side) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Info Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Contact Information</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 mb-0.5">Email Us</p>
                                <a href="mailto:support@sharestrength.test" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">support@sharestrength.test</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-purple-50 p-3 rounded-xl text-purple-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 mb-0.5">Call Us</p>
                                <p class="text-sm text-slate-600 font-medium">+1 (555) 123-4567</p>
                                <p class="text-xs text-slate-400 mt-1">Mon-Fri, 9am - 5pm EST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="bg-teal-50 p-3 rounded-xl text-teal-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 mb-0.5">Office</p>
                                <p class="text-sm text-slate-600 leading-relaxed">123 Accessibility Lane,<br>Tech City, TC 90210</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Link -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl shadow-md p-6 text-white text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <h3 class="font-bold text-lg mb-2 relative z-10">Check our FAQ</h3>
                    <p class="text-indigo-100 text-sm mb-5 relative z-10">Find instant answers to common questions about payments, tasks, and settings.</p>
                    <button class="w-full bg-white text-indigo-700 py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-50 transition-colors shadow-sm relative z-10">
                        View Help Center
                    </button>
                </div>
            </div>

            <!-- Ticket Form (Right Side) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    
                    @if($success)
                        <!-- Success State -->
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-8 text-center animate-fade-in-up">
                            <div class="bg-emerald-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5 shadow-sm">
                                <svg class="w-10 h-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h3 class="text-2xl font-black text-emerald-800 tracking-tight">Ticket Submitted!</h3>
                            <p class="text-emerald-700 mt-2 text-sm sm:text-base max-w-md mx-auto">Your support ticket has been securely logged. Our team will review it and reply to your registered email address shortly.</p>
                            
                            <div class="mt-8 flex justify-center gap-4">
                                <a href="{{ auth()->guard('helpmate')->check() ? route('helpmate.dashboard') : route('dashboard') }}" class="bg-white border border-emerald-200 text-emerald-700 font-bold py-2.5 px-6 rounded-xl hover:bg-emerald-100 transition-colors text-sm">
                                    Return to Dashboard
                                </a>
                                <button wire:click="$set('success', false)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-colors text-sm">
                                    Open Another Ticket
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Form State -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-6">
                            <h2 class="text-xl font-bold text-slate-800">Open a Support Ticket</h2>
                            <span class="bg-indigo-50 text-indigo-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Fast Response</span>
                        </div>
                        
                        <form wire:submit.prevent="submit" class="space-y-6">
                            
                            <!-- Logged In User Badge -->
                            @if($user)
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-4">
                                @php
                                    $imgSrc = 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=4F46E5&background=EEF2FF';
                                    if(!empty($user->profile_photo_url)) $imgSrc = $user->profile_photo_url;
                                    elseif(!empty($user->profile_photo)) $imgSrc = asset('storage/' . $user->profile_photo);
                                @endphp
                                <img src="{{ $imgSrc }}" alt="Profile" class="w-12 h-12 rounded-full border border-slate-200 object-cover bg-white">
                                <div>
                                    <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Submitting As</p>
                                    <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Issue Topic <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select wire:model="subject" class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none appearance-none transition-shadow text-slate-700 font-medium shadow-sm">
                                        <option value="" disabled selected>Select the category that best fits your issue...</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Technical Issue">Technical Issue or Bug</option>
                                        <option value="Billing/Payments">Billing or Payments</option>
                                        <option value="Report User">Report a User or Activity</option>
                                        <option value="Feature Request">Feature Request / Feedback</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                                @error('subject') <span class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Description <span class="text-red-500">*</span></label>
                                <textarea wire:model="message" rows="6" class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none transition-shadow text-slate-700 shadow-sm" placeholder="Please provide as much detail as possible so we can best assist you..."></textarea>
                                @error('message') <span class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-2">
                                <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-md shadow-indigo-200 transition-all transform active:scale-95 disabled:opacity-70 disabled:active:scale-100">
                                    <span wire:loading.remove>Submit Ticket</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Submitting...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>