@php
    // Define color themes based on account type
    $gradient = $isHelpmate ? 'from-emerald-500 to-green-500' : 'from-purple-500 to-indigo-600';
    $btnPrimary = $isHelpmate ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200 focus:ring-emerald-100' : 'bg-purple-600 hover:bg-purple-700 shadow-purple-200 focus:ring-purple-100';
    $textPrimary = $isHelpmate ? 'text-emerald-600' : 'text-purple-600';
    $hoverLight = $isHelpmate ? 'hover:bg-emerald-50 hover:text-emerald-700' : 'hover:bg-purple-50 hover:text-purple-700';
    $ringInput = $isHelpmate ? 'focus:ring-emerald-500 focus:border-emerald-500' : 'focus:ring-purple-500 focus:border-purple-500';
    $badge = $isHelpmate ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-purple-50 text-purple-700 border-purple-100';
    $borderColor = $isHelpmate ? 'border-emerald-600' : 'border-purple-600';
    $hoverBorder = $isHelpmate ? 'hover:border-emerald-300' : 'hover:border-purple-300';
    $fallbackHex = $isHelpmate ? '10B981' : '8B5CF6'; // For the UI-Avatar
@endphp

<div class="min-h-screen bg-slate-50 font-sans pb-12">
    <!-- Sleek Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ $isHelpmate ? route('helpmate.dashboard') : route('dashboard') }}"
                    class="p-2 -ml-2 text-slate-400 {{ $hoverLight }} rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-slate-800">Account Settings</h1>
            </div>
            <button wire:click="logout"
                class="text-sm font-medium text-slate-500 hover:text-red-600 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Log Out
            </button>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <!-- Success Message -->
        @if (session()->has('success'))
            <div
                class="mb-6 p-4 bg-teal-50 border border-teal-100 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="bg-teal-100 p-1.5 rounded-full">
                    <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-teal-800 font-medium text-sm">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Column: Profile Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <!-- Cover Photo Area (Dynamic Gradient) -->
                    <div class="h-32 bg-gradient-to-r {{ $gradient }}"></div>

                    <div class="px-6 pb-6 relative">
                        <!-- Avatar & Camera Button -->
                        <div class="flex justify-center -mt-16 mb-4">
                            <div class="relative group">
                                @php
                                    $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=white&background=' . $fallbackHex;

                                    if ($profilePhoto) {
                                        $imgSrc = $profilePhoto->temporaryUrl();
                                    } elseif (!empty($user->profile_photo_url)) {
                                        $imgSrc = $user->profile_photo_url;
                                    } elseif (!empty($user->profile_photo)) {
                                        $imgSrc = str_starts_with($user->profile_photo, 'http') || str_starts_with($user->profile_photo, '/storage')
                                            ? $user->profile_photo
                                            : asset('storage/' . $user->profile_photo);
                                    } else {
                                        $imgSrc = $fallbackAvatar;
                                    }
                                @endphp

                                <img src="{{ $imgSrc }}" alt="{{ $user->name }}"
                                    onerror="this.onerror=null; this.src='{{ $fallbackAvatar }}';"
                                    class="w-32 h-32 rounded-full border-4 border-white shadow-md object-cover bg-white">

                                <!-- Clickable Camera Icon -->
                                <label for="profilePhotoUpload"
                                    class="absolute bottom-1 right-1 {{ $btnPrimary }} text-white rounded-full p-2 cursor-pointer shadow-lg ring-4 ring-white transition-transform group-hover:scale-105">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <input type="file" id="profilePhotoUpload" wire:model="profilePhoto"
                                        accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="text-center">
                            <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                            <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                            <div class="mt-4 flex justify-center">
                                <span
                                    class="px-3 py-1 border rounded-full text-xs font-semibold tracking-wide uppercase {{ $badge }}">
                                    {{ $isHelpmate ? 'HelpMate Caregiver' : 'Registered User' }}
                                </span>
                            </div>
                        </div>

                        <!-- Loading State for Image Upload -->
                        <div wire:loading wire:target="profilePhoto" class="mt-4 text-center">
                            <span class="text-sm {{ $textPrimary }} font-medium flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Uploading preview...
                            </span>
                        </div>
                        @if($profilePhoto)
                            <p class="text-xs text-center {{ $textPrimary }} mt-4 font-medium">New photo selected. Don't
                                forget to save!</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Forms -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Personal Information Form -->
                <form wire:submit.prevent="updateProfile"
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Personal Information</h3>
                        <p class="text-sm text-slate-500 mt-1">Update your photo and personal details here.</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="name"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address <span
                                        class="text-red-500">*</span></label>
                                <input type="email" wire:model="email"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone Number</label>
                                <input type="tel" wire:model="phone"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Location (City,
                                    State)</label>
                                <input type="text" wire:model="location" placeholder="e.g. Springfield, IL"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Street Address</label>
                            <input type="text" wire:model="address"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                            @error('address') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($isHelpmate)
                                                <div data-skills="{{ json_encode($availableSkills) }}" x-data="{
                                availableSkills: [],
                                skillString: $wire.entangle('skills').live,

                                init() {
                                    // Safely parse the array from the HTML attribute
                                    this.availableSkills = JSON.parse(this.$el.dataset.skills);
                                },

                                get selectedArray() {
                                    let str = this.skillString || '';
                                    str = str.replace(/[\[\]\x22]/g, ''); 
                                    return str.split(',').map(s => s.trim()).filter(s => s !== '');
                                },

                                toggleSkill(skill) {
                                    let arr = this.selectedArray;
                                    if (arr.includes(skill)) {
                                        arr = arr.filter(s => s !== skill); 
                                    } else {
                                        arr.push(skill); 
                                    }
                                    this.skillString = arr.join(', ');
                                }
                            }">
                                                    <label class="block text-sm font-semibold text-slate-700 mb-3">Skills &
                                                        Qualifications</label>

                                                    <!-- Clickable Skill Pills (Dynamic Colors) -->
                                                    <div class="flex flex-wrap gap-2 mb-4">
                                                        <template x-for="skill in availableSkills" :key="skill">
                                                            <button type="button" @click="toggleSkill(skill)" :class="selectedArray.includes(skill) 
                                            ? '{{ $btnPrimary }} text-white border-transparent' 
                                            : 'bg-white text-slate-600 border-slate-200 {{ $hoverLight }} {{ $hoverBorder }}'"
                                                                class="px-4 py-2 rounded-full border text-sm font-semibold transition-all transform active:scale-95"
                                                                x-text="skill">
                                                            </button>
                                                        </template>
                                                    </div>

                                                    <label
                                                        class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Custom
                                                        Skills (Optional)</label>
                                                    <input type="text" x-model="skillString" placeholder="e.g. Sign Language, Pet Care..."
                                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                                    <p class="text-xs text-slate-500 mt-1.5">You can select from above or type comma-separated
                                                        skills here.</p>

                                                    @error('skills') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                        @else
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Disability Type / Support
                                    Needed</label>
                                <input type="text" wire:model="disability_type"
                                    placeholder="Mobility, Visual, Hearing, etc."
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('disability_type') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">About Me (Bio)</label>
                            <textarea wire:model="bio" rows="4"
                                placeholder="Tell the community a little bit about yourself..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm resize-none {{ $ringInput }}"></textarea>
                            @error('bio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled" wire:target="updateProfile"
                                class="inline-flex items-center justify-center px-6 py-2.5 {{ $btnPrimary }} text-white font-medium text-sm rounded-lg transition-all shadow-md disabled:opacity-70">
                                <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                                <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Change Password Form -->
                <form wire:submit.prevent="updatePassword"
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800">Security</h3>
                        <p class="text-sm text-slate-500 mt-1">Ensure your account is using a long, random password to
                            stay secure.</p>
                    </div>

                    <div class="space-y-5">
                        <div class="max-w-md">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Current Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" wire:model="current_password"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                            @error('current_password') <span
                            class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">New Password <span
                                        class="text-red-500">*</span></label>
                                <input type="password" wire:model="new_password"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                                @error('new_password') <span
                                class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm New Password
                                    <span class="text-red-500">*</span></label>
                                <input type="password" wire:model="new_password_confirmation"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 transition-colors sm:text-sm {{ $ringInput }}">
                            </div>
                        </div>

                        <div class="pt-4 flex justify-start">
                            <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                                class="inline-flex items-center justify-center px-6 py-2.5 bg-slate-800 text-white font-medium text-sm rounded-lg hover:bg-slate-900 focus:ring-4 focus:ring-slate-200 transition-all shadow-md disabled:opacity-70">
                                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>