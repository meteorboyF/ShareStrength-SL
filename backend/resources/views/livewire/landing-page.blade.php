<div class="font-sans text-neutral-dark antialiased overflow-x-hidden relative"
    x-data="{ isScrolled: false, mobileMenuOpen: false }">

    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 py-4 transition-all duration-300"
        :class="{ 'bg-white/90 backdrop-blur-md shadow-md': isScrolled }"
        @scroll.window="isScrolled = (window.pageYOffset > 10)">
        <div class="container mx-auto px-6 flex justify-between items-center max-w-7xl">
            <a href="#" class="flex items-center">
                <img src="{{ asset('img/logo2.png') }}" alt="Logo" class="h-10">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-4">
                <a href="/login" class="font-semibold hover:text-neutral-200 transition-colors"
                    :class="isScrolled ? 'text-neutral-dark' : 'text-white'">Login</a>
                <a href="/register-helpmate"
                    class="bg-primary text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-primary-dark transition transform hover:-translate-y-0.5">Become
                    a HelpMate</a>
            </div>

            <!-- Mobile Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-md focus:outline-none"
                :class="isScrolled ? 'text-neutral-darkest' : 'text-white'">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" style="display: none;"
            class="md:hidden bg-white mt-2 border-t absolute w-full left-0 top-full shadow-lg"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            @click.away="mobileMenuOpen = false">
            <a href="/login" class="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Login</a>
            <a href="/register-helpmate"
                class="block text-neutral-dark font-semibold px-6 py-3 hover:bg-neutral-light">Become a HelpMate</a>
        </div>
    </header>

    <main>
        <!-- Hero -->
        <section class="relative bg-cover bg-center pt-32 pb-20 md:pt-48 md:pb-32 text-center overflow-hidden"
            style="background-image: url('/img/indexbg.jpg')">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="container mx-auto px-6 max-w-4xl relative z-10" x-data
                x-intersect="$el.classList.add('is-visible')">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight opacity-0 translate-y-4 transition-all duration-1000 ease-out"
                    :class="$el.classList.contains('is-visible') ? 'opacity-100 translate-y-0' : ''">Reliable Help,
                    Right at Your Fingertips.</h1>

                <p class="text-lg md:text-xl text-neutral-light max-w-2xl mx-auto mb-10 opacity-0 translate-y-4 transition-all duration-1000 ease-out delay-150"
                    :class="$el.classList.contains('is-visible') ? 'opacity-100 translate-y-0' : ''">
                    ShareStrength is the trusted platform connecting individuals with disabilities to a community of
                    vetted, skilled, and compassionate helpmates for everyday tasks and specialized support.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center opacity-0 translate-y-4 transition-all duration-1000 ease-out delay-300"
                    :class="$el.classList.contains('is-visible') ? 'opacity-100 translate-y-0' : ''">
                    <a href="/register-user"
                        class="bg-primary text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-primary-dark hover:-translate-y-1 transform transition">
                        Find a HelpMate Today
                    </a>
                    <a href="#how-it-works"
                        class="bg-white/90 text-primary-dark font-bold py-4 px-8 rounded-xl shadow-lg hover:bg-white hover:-translate-y-1 transform border border-primary/20 transition">Learn
                        How It Works</a>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-20 md:py-28 bg-white">
            <div class="container mx-auto px-6 max-w-7xl text-center">
                <p class="text-sm font-bold text-primary uppercase tracking-wider mb-3 animate-fade-in-up">THE CHALLENGE
                </p>
                <h2 className="text-3xl md:text-4xl font-extrabold text-neutral-darkest mb-16 animate-fade-in-up delay-150"
                    style="animation-delay: 150ms;">Finding Trustworthy Help Shouldn't Be Hard</h2>

                <div class="grid md:grid-cols-3 gap-8 text-left">
                    <div class="bg-neutral-light p-8 rounded-2xl animate-fade-in-up delay-300"
                        style="animation-delay: 300ms;">
                        <div
                            class="mb-5 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <!-- Icon 1 -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-neutral-darkest mb-3">Uncertainty and Safety</h3>
                        <p class="text-neutral-medium leading-relaxed">Inviting someone new requires trust. We make it
                            easy to verify who is qualified, reliable, and safe.</p>
                    </div>

                    <div class="bg-neutral-light p-8 rounded-2xl animate-fade-in-up delay-[450ms]"
                        style="animation-delay: 450ms;">
                        <div
                            class="mb-5 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <!-- Icon 2 -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-neutral-darkest mb-3">Finding the Right Skills</h3>
                        <p class="text-neutral-medium leading-relaxed">Your needs are unique. Our platform helps you
                            find someone with specific skills without the hassle.</p>
                    </div>

                    <div class="bg-neutral-light p-8 rounded-2xl animate-fade-in-up delay-[600ms]"
                        style="animation-delay: 600ms;">
                        <div
                            class="mb-5 inline-flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 text-primary">
                            <!-- Icon 3 -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-neutral-darkest mb-3">Inflexible Arrangements</h3>
                        <p class="text-neutral-medium leading-relaxed">Traditional agencies can be rigid. Here, you get
                            the flexibility to find help on your schedule.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-20 md:py-28 bg-neutral-light">
            <div class="container mx-auto px-6 max-w-7xl">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-neutral-darkest">Get Support in 4 Simple Steps
                    </h2>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-start" x-data="{ 
                    activeStepId: 1, 
                    steps: [
                        { id: 1, title: '1. Post Your Task', desc: 'Describe what you need...', img: 'https://placehold.co/600x450/6D28D9/FFFFFF?text=1.+Post+Your+Task' },
                        { id: 2, title: '2. Connect', desc: 'Receive applications...', img: 'https://placehold.co/600x450/5B21B6/FFFFFF?text=2.+Connect' },
                        { id: 3, title: '3. Get Support', desc: 'Coordinate with your HelpMate...', img: 'https://placehold.co/600x450/4C1D95/FFFFFF?text=3.+Get+Support' },
                        { id: 4, title: '4. Pay Securely', desc: 'Approve payment securely...', img: 'https://placehold.co/600x450/3730A3/FFFFFF?text=4.+Pay+Securely' }
                    ],
                    get activeStep() { return this.steps.find(s => s.id === this.activeStepId) }
                 }">
                    <!-- Dynamic Image Area -->
                    <div class="lg:sticky top-28 h-96 lg:h-[30rem]">
                        <div class="w-full h-full rounded-2xl shadow-2xl bg-cover bg-center transition-all duration-500"
                            :style="`background-image: url('${activeStep.img}')`"></div>
                    </div>

                    <!-- Steps List -->
                    <div class="flex flex-col gap-6">
                        <template x-for="step in steps" :key="step.id">
                            <div @click="activeStepId = step.id"
                                class="p-6 rounded-xl cursor-pointer border-2 transition-all duration-300"
                                :class="activeStepId === step.id ? 'bg-white border-primary/50 shadow-lg' : 'border-transparent hover:bg-white/50'">
                                <div class="flex items-start gap-5">
                                    <div
                                        class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-full bg-primary/10 text-primary">
                                        <span class="font-bold" x-text="step.id"></span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg text-neutral-darkest mb-1" x-text="step.title">
                                        </h4>
                                        <p class="text-neutral-medium" x-text="step.desc"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-darkest text-white border-t border-neutral-dark">
        <div class="container mx-auto px-6 py-12 max-w-7xl">
            <!-- Main Footer Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">

                <!-- Column 1: Brand & Logo -->
                <div class="col-span-1 md:col-span-1">
                    <a href="#" class="flex items-center gap-3 mb-4">
                        <img src="/img/logo2.png" alt="ShareStrength" class="h-10 w-auto object-contain">
                    </a>
                    <p class="text-neutral-medium text-sm leading-relaxed">
                        Your independence, supported. connecting you to trusted help for everyday tasks.
                    </p>
                </div>

                <!-- Column 2: Platform -->
                <div>
                    <h5 class="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Platform</h5>
                    <div class="flex flex-col gap-3">
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">How it
                            Works</a>
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Find
                            Help</a>
                        <a href="/register-helpmate"
                            class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Become a
                            HelpMate</a>
                    </div>
                </div>

                <!-- Column 3: Company -->
                <div>
                    <h5 class="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Company</h5>
                    <div class="flex flex-col gap-3">
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">About
                            Us</a>
                        <a href="#"
                            class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Careers</a>
                        <a href="#"
                            class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Press</a>
                    </div>
                </div>

                <!-- Column 4: Support -->
                <div>
                    <h5 class="font-bold tracking-wider uppercase mb-6 text-neutral-400 text-xs">Support</h5>
                    <div class="flex flex-col gap-3">
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Help
                            Center</a>
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Trust &
                            Safety</a>
                        <a href="#" class="text-neutral-300 hover:text-primary-light transition-colors text-sm">Contact
                            Us</a>
                    </div>
                </div>
            </div>

            <!-- Copyright Bar -->
            <div class="mt-16 pt-8 border-t border-neutral-800 text-center">
                <p class="text-neutral-500 text-sm">&copy; 2025 ShareStrength. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>