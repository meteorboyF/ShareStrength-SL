<div class="min-h-screen flex items-center justify-center p-4 bg-gray-50 font-sans">
    <div class="w-full max-w-5xl mx-auto animate-fade-in-up">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">

            <!-- Left Side: Information & Tips (Purple Background) -->
            <div class="p-8 bg-purple-700 text-white order-last md:order-first flex flex-col justify-between">
                <div>
                    <a href="{{ route('dashboard') }}"
                        class="text-sm font-semibold text-purple-200 hover:text-white flex items-center gap-1 mb-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        Back to Portal
                    </a>
                    <h1 class="text-3xl font-bold tracking-tight">Post a New Task</h1>
                    <p class="mt-4 text-purple-200">Describe the support you need, and let our community of verified
                        HelpMates find you.</p>

                    <div class="mt-8 pt-6 border-t border-purple-500 border-opacity-50 space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0"><svg class="h-6 w-6 text-purple-300" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg></div>
                            <div>
                                <h4 class="font-semibold">Be Specific</h4>
                                <p class="text-sm text-purple-200">Clearly describe the task, including any specific
                                    requirements or times.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0"><svg class="h-6 w-6 text-purple-300" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg></div>
                            <div>
                                <h4 class="font-semibold">Offer a Fair Rate</h4>
                                <p class="text-sm text-purple-200">A competitive hourly rate will attract more qualified
                                    and experienced HelpMates.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-shrink-0"><svg class="h-6 w-6 text-purple-300" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 13.036h.008v.008h-.008v-.008z" />
                                </svg></div>
                            <div>
                                <h4 class="font-semibold">Safety First</h4>
                                <p class="text-sm text-purple-200">Remember, all HelpMates are verified by our platform
                                    for your peace of mind.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form -->
            <div class="p-8">
                <form wire:submit="postTask" class="space-y-8">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Task Title</label>
                        <input type="text" id="title" wire:model="title"
                            class="block w-full rounded-lg border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/50"
                            placeholder="A clear and concise title" required />
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                        <textarea id="description" wire:model="description" rows="4"
                            class="block w-full rounded-lg border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/50"
                            placeholder="Describe the task in detail..." required></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Skill Category Grid -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Skill Category</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['Physical Assistance', 'Transport & Errands', 'Household Help', 'Companionship', 'Tech & Admin', 'Other Support'] as $skill)
                                <div wire:click="$set('selectedSkill', '{{ $skill }}')" class="cursor-pointer">
                                    <div
                                        class="p-3 border-2 rounded-lg flex flex-col items-center justify-center gap-2 text-center transition-all duration-200 
                                            {{ $selectedSkill === $skill ? 'border-purple-600 bg-purple-50 text-purple-700 ring-2 ring-purple-600/50' : 'border-gray-200 hover:border-purple-500/50 hover:bg-gray-50 text-gray-500' }}">
                                        <svg class="h-8 w-8 {{ $selectedSkill === $skill ? 'text-purple-600' : 'text-gray-400' }}"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span
                                            class="text-xs font-semibold {{ $selectedSkill === $skill ? 'text-purple-700' : 'text-gray-900' }}">
                                            {{ $skill }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Urgency and Rate Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Urgency</label>
                            <div class="flex rounded-md shadow-sm">
                                @foreach(['Low', 'Medium', 'High'] as $index => $level)
                                    <button type="button" wire:click="$set('urgency', '{{ strtolower($level) }}')" class="flex-1 px-4 py-2 text-sm font-medium border transition-colors duration-200
                                                {{ $index === 0 ? 'rounded-l-md' : '' }}
                                                {{ $index === 2 ? 'rounded-r-md' : '' }}
                                                {{ $urgency === strtolower($level) ? 'bg-purple-600 text-white border-purple-600 z-10' : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50' }}
                                            ">
                                        {{ $level }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label for="budget" class="block text-sm font-semibold text-gray-900 mb-2">Proposed Rate
                                ($/hr)</label>
                            <div class="relative">
                                <input type="number" id="budget" wire:model="budget"
                                    class="block w-full rounded-lg border-gray-200 bg-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:border-purple-600 focus:ring-2 focus:ring-purple-600/50 text-center font-bold"
                                    step="1" min="10" max="100" required />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-semibold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600 transition-all transform hover:scale-105">
                            <span wire:loading.remove>Post Your Task</span>
                            <span wire:loading>Posting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>