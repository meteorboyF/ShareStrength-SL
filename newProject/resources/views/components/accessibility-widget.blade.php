{{-- Accessibility Widget - Font Size & Display Modes --}}
<div
    x-data="{
        isOpen: false,
        fontSize: localStorage.getItem('accessibility-font-size') || 'medium',
        displayMode: localStorage.getItem('accessibility-display-mode') || 'normal',
        eyeTrackingEnabled: localStorage.getItem('accessibility-eye-tracking') === 'true',
        eyeTrackingLoading: false,
        syncEnabled: localStorage.getItem('accessibility-sync') === 'true',
        syncLoading: false,

        modes: [
            { id: 'normal', label: 'Normal', icon: 'sun', desc: 'Default light theme' },
            { id: 'dark-mode', label: 'Dark', icon: 'moon', desc: 'Soft dark theme, easy on eyes' },
            { id: 'high-contrast', label: 'High Contrast', icon: 'contrast', desc: 'Maximum contrast for low vision' },
            { id: 'comfort-mode', label: 'Comfort', icon: 'eye', desc: 'Warm tones, larger text, more spacing' }
        ],

        csrf() {
            return document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
        },

        init() {
            this.applySettings();
            if (this.syncEnabled) {
                this.loadFromAccount();
            }
            if (this.eyeTrackingEnabled) {
                this.loadEyeTracking();
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

        async loadFromAccount() {
            this.syncLoading = true;
            try {
                const resp = await fetch('/accessibility/settings', {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = await resp.json().catch(() => ({}));
                const s = data?.settings;
                if (resp.ok && s) {
                    if (s.fontSize) {
                        this.fontSize = s.fontSize;
                        localStorage.setItem('accessibility-font-size', this.fontSize);
                    }
                    if (s.displayMode) {
                        this.displayMode = s.displayMode;
                        localStorage.setItem('accessibility-display-mode', this.displayMode);
                    }
                    if (typeof s.eyeTrackingEnabled === 'boolean') {
                        this.eyeTrackingEnabled = s.eyeTrackingEnabled;
                        localStorage.setItem('accessibility-eye-tracking', this.eyeTrackingEnabled.toString());
                    }
                    this.applySettings();
                }
            } finally {
                this.syncLoading = false;
            }
        },

        async saveToAccount() {
            if (!this.syncEnabled) return;

            try {
                const resp = await fetch('/accessibility/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        fontSize: this.fontSize,
                        displayMode: this.displayMode,
                        eyeTrackingEnabled: this.eyeTrackingEnabled,
                    }),
                });
                if (resp.status === 401) {
                    this.syncEnabled = false;
                    localStorage.setItem('accessibility-sync', 'false');
                }
            } catch (e) {
                // Best-effort only.
            }
        },

        setFontSize(size) {
            this.fontSize = size;
            localStorage.setItem('accessibility-font-size', size);
            this.applySettings();
            this.saveToAccount();
        },

        setDisplayMode(mode) {
            this.displayMode = mode;
            localStorage.setItem('accessibility-display-mode', mode);
            this.applySettings();
            this.saveToAccount();
        },

        loadEyeTracking() {
            if (window.EyeTrackingLoaded || this.eyeTrackingLoading) {
                if (window.EyeTracking && this.eyeTrackingEnabled && !window.EyeTracking.isTracking()) {
                    window.EyeTracking.start();
                }
                return;
            }

            this.eyeTrackingLoading = true;
            const script = document.createElement('script');
            script.src = '/js/eye-tracking.js';
            script.onload = () => {
                this.eyeTrackingLoading = false;
                if (window.EyeTracking && this.eyeTrackingEnabled) {
                    window.EyeTracking.start();
                }
            };
            script.onerror = () => {
                this.eyeTrackingLoading = false;
                this.eyeTrackingEnabled = false;
                localStorage.setItem('accessibility-eye-tracking', 'false');
            };
            document.body.appendChild(script);
        },

        toggleEyeTracking() {
            this.eyeTrackingEnabled = !this.eyeTrackingEnabled;
            localStorage.setItem('accessibility-eye-tracking', this.eyeTrackingEnabled.toString());

            if (this.eyeTrackingEnabled) {
                this.loadEyeTracking();
            } else if (window.EyeTracking && window.EyeTracking.isTracking()) {
                window.EyeTracking.stop();
            }

            this.saveToAccount();
        },

        toggleSync() {
            this.syncEnabled = !this.syncEnabled;
            localStorage.setItem('accessibility-sync', this.syncEnabled.toString());
            if (this.syncEnabled) {
                this.loadFromAccount();
                this.saveToAccount();
            }
        },

        isDark() {
            return this.displayMode === 'dark-mode' || this.displayMode === 'high-contrast';
        }
    }"
    class="fixed bottom-4 right-4 z-50"
>
    {{-- Settings Panel --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="mb-2 p-4 rounded-xl shadow-xl border"
        :class="isDark() ? 'bg-gray-900 border-gray-700' : 'bg-white border-gray-200'"
        style="min-width: 260px"
    >
        <h3
            class="text-sm font-bold mb-4 pb-2 border-b"
            :class="isDark() ? 'text-white border-gray-700' : 'text-gray-800 border-gray-200'"
        >
            Accessibility Settings
        </h3>

        {{-- Display Mode --}}
        <div class="mb-4">
            <label
                class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'"
            >
                Display Mode
            </label>
            <div class="grid grid-cols-2 gap-2">
                <template x-for="mode in modes" :key="mode.id">
                    <button
                        @click="setDisplayMode(mode.id)"
                        class="py-2 px-3 rounded-lg border transition-all text-xs font-medium flex items-center justify-center gap-2"
                        :class="displayMode === mode.id
                            ? (isDark() ? 'bg-purple-600 text-white border-purple-500' : 'bg-purple-600 text-white border-purple-600')
                            : (isDark() ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                    >
                        <template x-if="mode.icon === 'sun'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0l-1.414-1.414M7.05 7.05L5.636 5.636M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'moon'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'contrast'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m0 0a9 9 0 100-18v18z" />
                            </svg>
                        </template>
                        <template x-if="mode.icon === 'eye'">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </template>
                        <span x-text="mode.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Font Size --}}
        <div class="mb-4">
            <label
                class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'"
            >
                Text Size
            </label>
            <div class="flex gap-1">
                <template x-for="(size, index) in ['small', 'medium', 'large', 'x-large']" :key="size">
                    <button
                        @click="setFontSize(size)"
                        :title="size.charAt(0).toUpperCase() + size.slice(1).replace('-', ' ')"
                        class="flex-1 py-2 px-2 rounded-lg border transition-all font-medium"
                        :class="fontSize === size
                            ? (isDark() ? 'bg-purple-600 text-white border-purple-500' : 'bg-purple-600 text-white border-purple-600')
                            : (isDark() ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100')"
                        :style="'font-size: ' + (11 + index * 3) + 'px'"
                    >
                        A
                    </button>
                </template>
            </div>
        </div>

        {{-- Sync to account --}}
        <div class="mb-4">
            <label
                class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'"
            >
                Sync to Account
            </label>
            <button
                @click="toggleSync()"
                class="w-full py-2 px-3 rounded-lg border transition-all flex items-center justify-between text-sm"
                :class="isDark()
                    ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700'
                    : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'"
                :disabled="syncLoading"
            >
                <span x-text="syncLoading ? 'Loading...' : (syncEnabled ? 'Enabled' : 'Disabled')"></span>
                <div
                    class="w-10 h-5 rounded-full relative transition-colors"
                    :class="syncEnabled ? 'bg-green-500' : (isDark() ? 'bg-gray-600' : 'bg-gray-300')"
                >
                    <div
                        class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"
                        :class="syncEnabled ? 'translate-x-5' : 'translate-x-0.5'"
                    ></div>
                </div>
            </button>
        </div>

        {{-- Eye Tracking Toggle --}}
        <div>
            <label
                class="block text-xs font-semibold mb-2 uppercase tracking-wide"
                :class="isDark() ? 'text-gray-400' : 'text-gray-500'"
            >
                Eye Tracking
            </label>
            <button
                @click="toggleEyeTracking()"
                class="w-full py-2 px-3 rounded-lg border transition-all flex items-center justify-between text-sm"
                :class="isDark()
                    ? 'bg-gray-800 text-gray-300 border-gray-700 hover:bg-gray-700'
                    : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'"
                :disabled="eyeTrackingLoading"
            >
                <span x-text="eyeTrackingLoading ? 'Loading...' : (eyeTrackingEnabled ? 'Enabled' : 'Disabled')"></span>
                <div
                    class="w-10 h-5 rounded-full relative transition-colors"
                    :class="eyeTrackingEnabled ? 'bg-green-500' : (isDark() ? 'bg-gray-600' : 'bg-gray-300')"
                >
                    <div
                        class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"
                        :class="eyeTrackingEnabled ? 'translate-x-5' : 'translate-x-0.5'"
                    ></div>
                </div>
            </button>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button
        @click="isOpen = !isOpen"
        class="w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110"
        :class="isDark()
            ? 'bg-gray-800 text-white border-2 border-gray-600 hover:bg-gray-700'
            : 'bg-purple-600 text-white hover:bg-purple-700'"
        title="Accessibility Settings"
        aria-label="Accessibility Settings"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
            />
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            />
        </svg>
    </button>
</div>
