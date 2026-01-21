<div
    class="flex h-screen bg-gray-100"
    wire:poll.3s="pollMessages"
>
    <!-- Conversations List - Left Panel -->
    <div class="w-full md:w-96 bg-white border-r border-gray-200 flex flex-col {{ $selectedConversationId ? 'hidden md:flex' : 'flex' }}">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-white px-6 py-4 shadow-sm flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
            <a href="{{ $isHelpmate ? route('helpmate.dashboard') : route('dashboard') }}" class="text-sm text-gray-500 hover:text-purple-600">
                Back to Dashboard
            </a>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @if(empty($conversations))
                <div class="flex items-center justify-center h-full px-6">
                    <div class="text-center">
                        <svg class="mx-auto h-20 w-20 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No conversations yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Start a conversation by messaging someone from a task or their profile.
                        </p>
                    </div>
                </div>
            @else
                @foreach($conversations as $conv)
                    <div
                        wire:click="selectConversation({{ $conv['id'] }})"
                        wire:key="conv-{{ $conv['id'] }}"
                        class="px-6 py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition {{ $selectedConversationId == $conv['id'] ? 'bg-purple-50 border-l-4 border-l-purple-600' : '' }}"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ $conv['other_user']['profile_photo_url'] ?? $conv['other_user']['profile_photo'] ?? 'https://placehold.co/50' }}"
                                alt="{{ $conv['other_user']['name'] ?? 'User' }}"
                                class="w-12 h-12 rounded-full object-cover"
                            />
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $conv['other_user']['name'] ?? 'Unknown User' }}</h3>
                                @if($conv['task'])
                                    <p class="text-xs text-purple-600 truncate">{{ $conv['task']['title'] ?? '' }}</p>
                                @endif
                                <p class="text-xs text-gray-500">
                                    {{ $conv['last_message_at'] ? \Carbon\Carbon::parse($conv['last_message_at'])->diffForHumans() : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Chat Window - Right Panel -->
    <div class="flex-1 flex flex-col {{ $selectedConversationId ? 'flex' : 'hidden md:flex' }}">
        @if(!$selectedConversationId)
            <div class="flex items-center justify-center h-full bg-gray-50">
                <div class="text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Select a conversation</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Choose a conversation from the list to start messaging
                    </p>
                </div>
            </div>
        @else
            <!-- Chat Header -->
            <div class="border-b border-gray-200 bg-white px-6 py-4 shadow-sm flex items-center gap-4">
                <button
                    wire:click="$set('selectedConversationId', null)"
                    class="md:hidden p-2 hover:bg-gray-100 rounded-full"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $selectedConversation['other_user']['name'] ?? 'Unknown' }}
                    </h2>
                    @if($selectedConversation['task'] ?? null)
                        <span class="text-xs text-purple-600">
                            Task: {{ $selectedConversation['task']['title'] ?? '' }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Messages List -->
            <div
                class="flex-1 overflow-y-auto px-6 py-4 bg-gray-50 space-y-4"
                id="messages-container"
                x-data
                x-init="$el.scrollTop = $el.scrollHeight"
                @scroll-to-bottom.window="$el.scrollTop = $el.scrollHeight"
            >
                @if(empty($messages))
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">No messages yet. Start the conversation!</p>
                    </div>
                @else
                    @foreach($messages as $message)
                        @php
                            $isOwn = $message['sender_id'] == $currentUserId && $message['sender_type'] == $currentUserType;
                        @endphp
                        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl {{ $isOwn ? 'bg-purple-600 text-white' : 'bg-white border border-gray-200 text-gray-900' }}">
                                <p class="text-sm">{{ $message['content'] }}</p>
                                <p class="text-xs mt-1 {{ $isOwn ? 'text-purple-200' : 'text-gray-400' }}">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('g:i A') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 bg-white px-6 py-4">
                <form
                    wire:submit="sendMessage"
                    class="flex gap-3"
                    x-data
                    x-on:message-sent.window="$refs.msgInput.value = ''"
                >
                    <input
                        type="text"
                        wire:model="newMessage"
                        x-ref="msgInput"
                        placeholder="Type a message..."
                        class="flex-1 px-4 py-3 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        autocomplete="off"
                    />
                    <button
                        type="submit"
                        class="bg-purple-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-purple-700 transition"
                    >
                        Send
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
