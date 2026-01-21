<div
    x-data="{
        isOpen: false,
        messages: [
            { id: 1, text: 'Hi! I am your HelpMate Assistant. How can I help today?', sender: 'bot' }
        ],
        inputText: '',
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messages) {
                    this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                }
            });
        },
        sendMessage() {
            const text = this.inputText.trim();
            if (!text) return;

            const userMsg = { id: Date.now(), text, sender: 'user' };
            this.messages.push(userMsg);
            this.inputText = '';
            this.scrollToBottom();

            setTimeout(() => {
                let botResponse = 'I am not connected to a real AI yet, but I can still help with common questions.';
                const lower = text.toLowerCase();
                if (lower.includes('price') || lower.includes('cost')) {
                    botResponse = 'HelpMates set their own hourly rates, typically between $20 and $50 per hour.';
                } else if (lower.includes('payment')) {
                    botResponse = 'You can manage payments securely through your dashboard under Pending Payments.';
                }

                this.messages.push({ id: Date.now() + 1, text: botResponse, sender: 'bot' });
                this.scrollToBottom();
            }, 800);
        }
    }"
    class="fixed bottom-6 right-24 z-50 flex flex-col items-end pointer-events-none"
>
    <template x-if="isOpen">
        <div class="bg-white w-80 h-96 rounded-2xl shadow-2xl border border-gray-200 mb-4 flex flex-col overflow-hidden pointer-events-auto">
            <div class="bg-purple-600 p-4 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <h3 class="font-bold text-sm">HelpMate Assistant</h3>
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
                        <div
                            class="max-w-[80%] rounded-xl p-3 text-sm"
                            :class="msg.sender === 'user'
                                ? 'bg-purple-600 text-white rounded-br-none'
                                : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none shadow-sm'"
                            x-text="msg.text"
                        ></div>
                    </div>
                </template>
            </div>

            <form @submit.prevent="sendMessage" class="p-3 bg-white border-t border-gray-200">
                <div class="flex gap-2">
                    <input
                        type="text"
                        x-model="inputText"
                        placeholder="Type a message..."
                        class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-600"
                    />
                    <button type="submit" class="bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 transition">
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
        class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-full shadow-lg transition transform hover:scale-105 pointer-events-auto flex items-center justify-center"
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
