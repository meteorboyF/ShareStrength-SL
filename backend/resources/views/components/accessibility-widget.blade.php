{{-- Accessibility Widget - Font Size & High Contrast --}}
<div
    x-data="{
        isOpen: false,
        fontSize: localStorage.getItem('accessibility-font-size') || 'medium',
        highContrast: localStorage.getItem('accessibility-high-contrast') === 'true',
        eyeTrackingEnabled: localStorage.getItem('accessibility-eye-tracking') === 'true',
        eyeTrackingLoading: false,

        init() {
            this.applySettings();
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

            // Add current font size class
            root.classList.add('font-' + this.fontSize);

            // Toggle high contrast
            if (this.highContrast) {
                root.classList.add('high-contrast');
            } else {
                root.classList.remove('high-contrast');
            }
        },

        setFontSize(size) {
            this.fontSize = size;
            localStorage.setItem('accessibility-font-size', size);
            this.applySettings();
        },

        toggleContrast() {
            this.highContrast = !this.highContrast;
            localStorage.setItem('accessibility-high-contrast', this.highContrast.toString());
            this.applySettings();
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
        class="mb-2 p-4 rounded-lg shadow-lg border"
        :class="highContrast ? 'bg-black border-white' : 'bg-white border-gray-200'"
        style="min-width: 200px"
    >
        {{-- Font Size --}}
        <div class="mb-4">
            <label
                class="block text-sm font-medium mb-2"
                :class="highContrast ? 'text-white' : 'text-gray-700'"
            >
                Text Size
            </label>
            <div class="flex gap-1">
                <template x-for="(size, index) in ['small', 'medium', 'large', 'x-large']" :key="size">
                    <button
                        @click="setFontSize(size)"
                        :title="size.charAt(0).toUpperCase() + size.slice(1)"
                        class="flex-1 py-2 px-2 rounded border transition-all"
                        :class="fontSize === size
                            ? (highContrast ? 'bg-white text-black border-white' : 'bg-purple-600 text-white border-purple-600')
                            : (highContrast ? 'bg-black text-white border-white hover:bg-gray-800' : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200')"
                        :style="'font-size: ' + (12 + index * 3) + 'px'"
                    >
                        A
                    </button>
                </template>
            </div>
        </div>

        {{-- High Contrast Toggle --}}
        <div>
            <label
                class="block text-sm font-medium mb-2"
                :class="highContrast ? 'text-white' : 'text-gray-700'"
            >
                High Contrast
            </label>
            <button
                @click="toggleContrast()"
                class="w-full py-2 px-4 rounded border transition-all flex items-center justify-between"
                :class="highContrast
                    ? 'bg-white text-black border-white'
                    : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
            >
                <span x-text="highContrast ? 'On' : 'Off'"></span>
                <div
                    class="w-10 h-5 rounded-full relative transition-colors"
                    :class="highContrast ? 'bg-green-500' : 'bg-gray-300'"
                >
                    <div
                        class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform"
                        :class="highContrast ? 'translate-x-5' : 'translate-x-0.5'"
                    ></div>
                </div>
            </button>
        </div>

        {{-- Eye Tracking Toggle --}}
        <div class="mt-4">
            <label
                class="block text-sm font-medium mb-2"
                :class="highContrast ? 'text-white' : 'text-gray-700'"
            >
                Eye Tracking
            </label>
            <button
                @click="toggleEyeTracking()"
                class="w-full py-2 px-4 rounded border transition-all flex items-center justify-between"
                :class="highContrast
                    ? 'bg-white text-black border-white'
                    : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                :disabled="eyeTrackingLoading"
            >
                <span x-text="eyeTrackingLoading ? 'Loading...' : (eyeTrackingEnabled ? 'On' : 'Off')"></span>
                <div
                    class="w-10 h-5 rounded-full relative transition-colors"
                    :class="eyeTrackingEnabled ? 'bg-green-500' : 'bg-gray-300'"
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
        :class="highContrast
            ? 'bg-white text-black border-2 border-white'
            : 'bg-purple-600 text-white'"
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
                d="M12 4.5c-4.5 0-7.5 3-8.5 5.5 1 2.5 4 5.5 8.5 5.5s7.5-3 8.5-5.5c-1-2.5-4-5.5-8.5-5.5z"
            />
            <circle cx="12" cy="10" r="3" stroke-width="2" />
            <path
                stroke-linecap="round"
                stroke-width="2"
                d="M12 15v5M9 18h6"
            />
        </svg>
    </button>
</div>
