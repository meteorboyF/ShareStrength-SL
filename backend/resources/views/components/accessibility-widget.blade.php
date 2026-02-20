{{-- Accessibility Widget - Font Size & Display Modes --}}
<div x-data="{
        isOpen: false,
        fontSize: localStorage.getItem('accessibility-font-size') || 'medium',
        displayMode: localStorage.getItem('accessibility-display-mode') || 'normal',
        eyeTrackingEnabled: localStorage.getItem('accessibility-eye-tracking') === 'true',
        voiceTrackingEnabled: localStorage.getItem('accessibility-voice-tracking') === 'true',
        eyeTrackingLoading: false,
        voiceTrackingLoading: false,

        modes: [
            { id: 'normal', label: 'Normal', icon: 'sun', desc: 'Default light theme' },
            { id: 'dark-mode', label: 'Dark', icon: 'moon', desc: 'Soft dark theme, easy on eyes' },
            { id: 'high-contrast', label: 'High Contrast', icon: 'contrast', desc: 'Maximum contrast for low vision' },
            { id: 'comfort-mode', label: 'Comfort', icon: 'eye', desc: 'Warm tones, larger text, more spacing' }
        ],

        init() {
            this.applySettings();

            // initialize global hard-disable flag
            if (typeof window.__EyeTrackingHardDisabled === 'undefined') {
                window.__EyeTrackingHardDisabled = !this.eyeTrackingEnabled;
            }

            // Enforce saved state on load
            if (this.eyeTrackingEnabled) {
                this.enableEyeTrackingHard();
            } else {
                this.disableEyeTrackingHard();
            }

            // initialize global hard-disable flag for voice tracking
            if (typeof window.__VoiceTrackingHardDisabled === 'undefined') {
                window.__VoiceTrackingHardDisabled = !this.voiceTrackingEnabled;
                }

                // Enforce saved state on load
            if (this.voiceTrackingEnabled) {
                 this.enableVoiceTrackingHard();
            } else {
                 this.disableVoiceTrackingHard();
                }
        },

        applySettings() {
            const root = document.documentElement;

            // Remove all font size classes
            ['font-small', 'font-medium', 'font-large', 'font-x-large'].forEach(cls => {
                root.classList.remove(cls);
            });

            // Remove all display mode classes
            ['dark-mode', 'high-contrast', 'comfort-mode'].forEach(cls => {
                root.classList.remove(cls);
            });

            // Add current font size class
            root.classList.add('font-' + this.fontSize);

            // Add current display mode class (if not normal)
            if (this.displayMode !== 'normal') {
                root.classList.add(this.displayMode);
            }
        },

        setFontSize(size) {
            this.fontSize = size;
            localStorage.setItem('accessibility-font-size', size);
            this.applySettings();
        },

        setDisplayMode(mode) {
            this.displayMode = mode;
            localStorage.setItem('accessibility-display-mode', mode);
            this.applySettings();
        },

        enableEyeTrackingHard() {
            window.__EyeTrackingHardDisabled = false;

            // If script already exists, clear its internal hard disable too
            if (window.EyeTracking && typeof window.EyeTracking.enable === 'function') {
                window.EyeTracking.enable();
            }

            this.loadEyeTracking();
        },

        disableEyeTrackingHard() {
            // hard guard so NOTHING can restart it
            window.__EyeTrackingHardDisabled = true;

            // If script exists, call its hard disable if available
            if (window.EyeTracking && typeof window.EyeTracking.disable === 'function') {
                window.EyeTracking.disable();
            }

            // stop immediately if it exists
            if (window.EyeTracking && typeof window.EyeTracking.stop === 'function') {
                window.EyeTracking.stop();
            }

            // extra: if camera stream still attached, kill tracks
            const v = document.querySelector('video.eyetracking-video');
            if (v && v.srcObject) {
                try { v.srcObject.getTracks().forEach(t => t.stop()); } catch (_) {}
                v.srcObject = null;
            }
        },

        loadEyeTracking() {
            // If user disabled, never load/start
            if (window.__EyeTrackingHardDisabled) return;

            // Already loaded? just start if enabled
            if (window.EyeTrackingLoaded || this.eyeTrackingLoading) {
                if (
                    window.EyeTracking &&
                    this.eyeTrackingEnabled &&
                    typeof window.EyeTracking.isTracking === 'function' &&
                    !window.EyeTracking.isTracking()
                ) {
                    window.EyeTracking.start();
                }
                return;
            }

            this.eyeTrackingLoading = true;

            const script = document.createElement('script');
            script.src = '/js/eye-tracking.js';

            script.onload = () => {
                this.eyeTrackingLoading = false;

                // If user toggled OFF while loading, don't start
                if (window.__EyeTrackingHardDisabled) return;

                if (window.EyeTracking && this.eyeTrackingEnabled) {
                    window.EyeTracking.start();
                }
            };

            script.onerror = () => {
                this.eyeTrackingLoading = false;
                this.eyeTrackingEnabled = false;
                localStorage.setItem('accessibility-eye-tracking', 'false');
                window.__EyeTrackingHardDisabled = true;
            };

            document.body.appendChild(script);
        },

        toggleEyeTracking() {
            if (this.eyeTrackingLoading) return;

            this.eyeTrackingEnabled = !this.eyeTrackingEnabled;
            localStorage.setItem('accessibility-eye-tracking', this.eyeTrackingEnabled.toString());

            if (this.eyeTrackingEnabled) {
                this.enableEyeTrackingHard();
            } else {
                this.disableEyeTrackingHard();
            }
        },


enableVoiceTrackingHard() {
    window.__VoiceTrackingHardDisabled = false;

    // If script already exists, clear its internal hard disable too (if implemented)
    if (window.VoiceTracking && typeof window.VoiceTracking.enable === 'function') {
        window.VoiceTracking.enable();
    }

    this.loadVoiceTracking();
},

disableVoiceTrackingHard() {
    window.__VoiceTrackingHardDisabled = true;

    if (window.VoiceTracking && typeof window.VoiceTracking.disable === 'function') {
        window.VoiceTracking.disable();
    }

    if (window.VoiceTracking) {
        try { window.VoiceTracking.stop?.(); } catch (_) {}
        try { window.VoiceTracking.destroy?.(); } catch (_) {}
    }
},
loadVoiceTracking() {
    // If user disabled, never load/start
    if (window.__VoiceTrackingHardDisabled) return;

    // Already loaded? just start if enabled
    if (window.VoiceTrackingLoaded || this.voiceTrackingLoading) {
        if (window.VoiceTracking && this.voiceTrackingEnabled) {
            try { window.VoiceTracking.start?.(); } catch (_) {}
        }
        return;
    }

    this.voiceTrackingLoading = true;

    const script = document.createElement('script');

    // IMPORTANT:
    // If SupaVoiceTracking.js uses `export ...` then it MUST be loaded as module:
    script.type = 'module';

    script.src = '/js/SupaVoiceTracking.js';

    script.onload = () => {
        this.voiceTrackingLoading = false;

        // If user toggled OFF while loading, don't start
        if (window.__VoiceTrackingHardDisabled) return;

        if (window.VoiceTracking && this.voiceTrackingEnabled) {
            try { window.VoiceTracking.start?.(); } catch (_) {}
        }
    };

    script.onerror = () => {
        this.voiceTrackingLoading = false;
        this.voiceTrackingEnabled = false;
        localStorage.setItem('accessibility-voice-tracking', 'false');
        window.__VoiceTrackingHardDisabled = true;
    };

    document.body.appendChild(script);
},

toggleVoiceTracking() {
    if (this.voiceTrackingLoading) return;

    this.voiceTrackingEnabled = !this.voiceTrackingEnabled;
    localStorage.setItem('accessibility-voice-tracking', this.voiceTrackingEnabled.toString());

    if (this.voiceTrackingEnabled) {
        this.enableVoiceTrackingHard();
    } else {
        this.disableVoiceTrackingHard();
    }
},

        isDark() {
            return this.displayMode === 'dark-mode' || this.displayMode === 'high-contrast';
        }
    }" class="fixed bottom-4 left-4 z-50">
    {{-- Settings Panel --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="mb-2 p-4 rounded-xl shadow-xl border origin-bottom-left"
        :class="isDark() ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'" style="min-width: 260px">
        <h3 class="text-sm font-bold mb-4 pb-2 border-b"
            :class="isDark() ? 'text-white border-gray-700' : 'text-gray-800 border-gray-200'">
            Accessibility Settings
        </h3>

        {{-- Display Mode --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'">
                Display Mode
            </label>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="mode in modes" :key="mode.id">
                    <button @click="setDisplayMode(mode.id)" :title="mode.desc"
                        class="py-2 px-3 rounded-lg border text-xs font-medium transition-all flex flex-col items-center gap-1"
                        :class="displayMode === mode.id
                            ? (isDark() ? 'bg-purple-600 text-white border-purple-500' : 'bg-purple-600 text-white border-purple-600')
                            : (isDark() ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')">
                        {{-- Icons --}}
                        <template x-if="mode.icon === 'sun'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'moon'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'contrast'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v18m0 0a9 9 0 100-18 9 9 0 000 18z" />
                                <path fill="currentColor" d="M12 3a9 9 0 000 18V3z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'eye'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <span x-text="mode.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Font Size --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'">
                Text Size
            </label>
            <div class="flex gap-1">
                <template x-for="(size, index) in ['small', 'medium', 'large', 'x-large']" :key="size">
                    <button @click="setFontSize(size)"
                        :title="size.charAt(0).toUpperCase() + size.slice(1).replace('-', ' ')"
                        class="flex-1 py-2 px-2 rounded-lg border transition-all font-medium"
                        :class="fontSize === size
                            ? (isDark() ? 'bg-purple-600 text-white border-purple-500' : 'bg-purple-600 text-white border-purple-600')
                            : (isDark() ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                        :style="'font-size: ' + (11 + index * 3) + 'px'">
                        A
                    </button>
                </template>
            </div>
        </div>

        {{-- Eye Tracking Toggle --}}
        <div>
            <label class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'">
                Eye Tracking
            </label>
            <button @click="toggleEyeTracking()"
                class="w-full py-2 px-3 rounded-lg border transition-all flex items-center justify-between text-sm"
                :class="isDark()
                    ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700'
                    : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'" :disabled="eyeTrackingLoading">
                <span x-text="eyeTrackingLoading ? 'Loading...' : (eyeTrackingEnabled ? 'Enabled' : 'Disabled')"></span>
                <div class="w-10 h-5 rounded-full relative transition-colors"
                    :class="eyeTrackingEnabled ? 'bg-green-500' : (isDark() ? 'bg-gray-600' : 'bg-gray-300')">
                    <div class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"
                        :class="eyeTrackingEnabled ? 'translate-x-5' : 'translate-x-0.5'"></div>
                </div>
            </button>
        </div>

        {{-- Voice Tracking Toggle --}}
        <div class="mt-4">
            <label class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'">
                Voice Tracking
            </label>

            <button @click="toggleVoiceTracking()"
                class="w-full py-2 px-3 rounded-lg border transition-all flex items-center justify-between text-sm"
                :class="isDark()
            ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700'
            : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'" :disabled="voiceTrackingLoading">
                <span
                    x-text="voiceTrackingLoading ? 'Loading...' : (voiceTrackingEnabled ? 'Enabled' : 'Disabled')"></span>

                <div class="w-10 h-5 rounded-full relative transition-colors"
                    :class="voiceTrackingEnabled ? 'bg-green-500' : (isDark() ? 'bg-gray-600' : 'bg-gray-300')">
                    <div class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"
                        :class="voiceTrackingEnabled ? 'translate-x-5' : 'translate-x-0.5'"></div>
                </div>
            </button>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button @click="isOpen = !isOpen"
        class="w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110" :class="isDark()
            ? 'bg-gray-800 text-white border-2 border-gray-600 hover:bg-gray-700'
            : 'bg-purple-600 text-white hover:bg-purple-700'" title="Accessibility Settings"
        aria-label="Accessibility Settings">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </button>
</div>