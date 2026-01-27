<div
    x-data="{
        isOpen: false,
        isLoading: false,
        askUrl: '{{ route('chatbot.ask') }}',
        historyUrl: '{{ route('chatbot.history') }}',
        streamUrl: '{{ route('chatbot.stream') }}',
        messages: [],
        inputText: '',
        csrf() {
            return document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
        },
        init() {
            this.loadHistory();
        },
        async loadHistory() {
            try {
                const resp = await fetch(this.historyUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const data = await resp.json().catch(() => ({}));
                if (resp.ok && Array.isArray(data?.messages) && data.messages.length) {
                    this.messages = data.messages;
                } else if (!this.messages.length) {
                    this.messages = [{ id: 1, text: 'Hi! I am your assistant. How can I help today?', sender: 'bot', links: [] }];
                }
                this.scrollToBottom();
            } catch (e) {
                if (!this.messages.length) {
                    this.messages = [{ id: 1, text: 'Hi! I am your assistant. How can I help today?', sender: 'bot', links: [] }];
                }
            }
        },
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messages) {
                    this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                }
            });
        },
        async sendMessage() {
            const text = this.inputText.trim();
            if (!text) return;

            const userMsg = { id: Date.now(), text, sender: 'user' };
            this.messages.push(userMsg);
            this.inputText = '';
            this.scrollToBottom();

            this.isLoading = true;

            try {
                const botMsg = { id: Date.now() + 1, text: '', sender: 'bot', links: [] };
                this.messages.push(botMsg);
                this.scrollToBottom();

                const resp = await fetch(this.streamUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'text/event-stream',
                        'X-CSRF-TOKEN': this.csrf(),
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: text }),
                });

                if (!resp.ok) {
                    const data = await resp.json().catch(() => ({}));
                    throw new Error(data?.message || 'Chat request failed');
                }

                if (!resp.body) {
                    throw new Error('Streaming not supported');
                }

                const reader = resp.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let buffer = '';
                let gotAnyDelta = false;

                const handleEventBlock = (block) => {
                    const lines = block.split('\n');
                    let eventName = 'message';
                    const dataLines = [];

                    for (const line of lines) {
                        if (line.startsWith('event:')) {
                            eventName = line.slice(6).trim();
                        } else if (line.startsWith('data:')) {
                            dataLines.push(line.slice(5).trim());
                        }
                    }

                    const dataStr = dataLines.join('\n').trim();
                    if (!dataStr) return;

                    let payload = null;
                    try {
                        payload = JSON.parse(dataStr);
                    } catch (e) {
                        payload = { text: dataStr };
                    }

                    if (eventName === 'links' && Array.isArray(payload?.links)) {
                        botMsg.links = payload.links;
                    } else if (eventName === 'delta' && typeof payload?.text === 'string') {
                        if (!gotAnyDelta && botMsg.text === 'Thinking…') {
                            botMsg.text = '';
                        }
                        botMsg.text += payload.text;
                        gotAnyDelta = true;
                    } else if (eventName === 'error') {
                        botMsg.text = payload?.message || 'Sorry — something went wrong.';
                    }
                };

                // Make it clear something is happening even before the first token arrives.
                botMsg.text = 'Thinking…';

                while (true) {
                    const { value, done } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, { stream: true });
                    buffer = buffer.replace(/\r\n/g, '\n');

                    let idx;
                    while ((idx = buffer.indexOf('\n\n')) !== -1) {
                        const block = buffer.slice(0, idx);
                        buffer = buffer.slice(idx + 2);
                        handleEventBlock(block);
                    }

                    this.scrollToBottom();
                }

                if (!gotAnyDelta && botMsg.text === 'Thinking…') {
                    botMsg.text = 'Sorry — I did not get a response.';
                }

                await this.loadHistory();
            } catch (e) {
                this.messages.push({
                    id: Date.now() + 1,
                    text: 'Sorry — the assistant is not available right now. Please try again in a moment.',
                    sender: 'bot',
                    links: [],
                });
            } finally {
                this.isLoading = false;
                this.scrollToBottom();
            }
        }
    }"
    class="fixed bottom-6 right-24 z-50 flex flex-col items-end pointer-events-none"
>
    <template x-if="isOpen">
        <div class="bg-white w-80 h-96 rounded-2xl shadow-2xl border border-gray-200 mb-4 flex flex-col overflow-hidden pointer-events-auto">
            <div class="bg-gray-900 p-4 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <h3 class="font-bold text-sm">Assistant</h3>
                </div>
                <button @click="isOpen = false" class="text-white/80 hover:text-white" type="button">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="messages">
                <template x-for="msg in messages" :key="msg.id">
                    <div class="flex" :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[80%]">
                            <div
                                class="rounded-xl p-3 text-sm"
                                :class="msg.sender === 'user'
                                    ? 'bg-gray-900 text-white rounded-br-none'
                                    : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none shadow-sm'"
                                x-text="msg.text"
                            ></div>

                            <template x-if="msg.sender === 'bot' && Array.isArray(msg.links) && msg.links.length">
                                <div class="mt-2 text-[11px] text-gray-500 space-y-1">
                                    <div class="font-semibold uppercase tracking-wide">Quick Links</div>
                                    <template x-for="s in msg.links" :key="(s.url || '') + (s.chunk_index ?? '')">
                                        <a
                                            class="inline-flex items-center gap-2 w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 hover:border-gray-900 hover:text-gray-900 transition truncate"
                                            :href="s.url"
                                        >
                                            <span class="truncate" x-text="s.title || s.url"></span>
                                            <span class="ml-auto text-[10px] text-gray-400">open</span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

            </div>

            <form @submit.prevent="sendMessage" class="p-3 bg-white border-t border-gray-200">
                <div class="flex gap-2">
                    <input
                        type="text"
                        x-model="inputText"
                        placeholder="Type a message..."
                        class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-gray-900"
                    />
                    <button type="submit" class="bg-gray-900 text-white p-2 rounded-lg hover:bg-black transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </template>

    <button
        @click="isOpen = !isOpen"
        class="bg-gray-900 hover:bg-black text-white p-4 rounded-full shadow-lg transition transform hover:scale-105 pointer-events-auto flex items-center justify-center"
        type="button"
    >
        <template x-if="isOpen">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </template>
        <template x-if="!isOpen">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
        </template>
    </button>
</div>
