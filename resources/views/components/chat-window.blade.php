<div x-data="chatWindow({{ $domainId ?? 'null' }}, {{ $otherUserId ?? 'null' }})" 
     x-init="init()"
     class="fixed bottom-4 right-4 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50"
     x-show="isOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">
    
    <!-- Chat Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium" x-text="otherUser?.name?.charAt(0) || '?'"></span>
            </div>
            <div>
                <h3 class="font-medium text-gray-900 dark:text-white" x-text="otherUser?.name || 'Chat'"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="isOnline ? 'Online' : 'Offline'"></p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="toggleMinimize" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </button>
            <button @click="close" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="h-80 overflow-y-auto p-4 space-y-4" x-ref="messagesContainer">
        <template x-for="message in messages" :key="message.id">
            <div class="flex" :class="message.from_user_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg" 
                     :class="message.from_user_id === {{ auth()->id() }} 
                        ? 'bg-blue-500 text-white' 
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white'">
                    <p class="text-sm" x-text="message.body"></p>
                    <p class="text-xs mt-1 opacity-75" x-text="formatTime(message.created_at)"></p>
                </div>
            </div>
        </template>
        
        <!-- Typing indicator -->
        <div x-show="isTyping" class="flex justify-start">
            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg px-4 py-2">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Input -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <form @submit.prevent="sendMessage" class="flex space-x-2">
            <input type="text" 
                   x-model="newMessage" 
                   @input="handleTyping"
                   placeholder="Type a message..." 
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                   :disabled="isLoading">
            <button type="submit" 
                    :disabled="!newMessage.trim() || isLoading"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
function chatWindow(domainId, otherUserId) {
    return {
        isOpen: false,
        isMinimized: false,
        isLoading: false,
        isTyping: false,
        isOnline: false,
        messages: [],
        newMessage: '',
        otherUser: null,
        domainId: domainId,
        otherUserId: otherUserId,
        typingTimeout: null,

        init() {
            this.loadMessages();
            this.setupEchoListeners();
            this.loadOtherUser();
        },

        setupEchoListeners() {
            if (!window.Echo) {
                console.error('Echo not available');
                return;
            }

            // Listen for new messages
            window.Echo.private(`user.${{{ auth()->id() }}}`)
                .listen('.message.sent', (e) => {
                    if (e.message.from_user_id === this.otherUserId || e.message.to_user_id === this.otherUserId) {
                        this.messages.push(e.message);
                        this.scrollToBottom();
                        this.markAsRead(e.message.id);
                    }
                });

            // Listen for message read events
            window.Echo.private(`user.${{{ auth()->id() }}}`)
                .listen('.message.read', (e) => {
                    // Handle read receipts if needed
                });

            // Listen for typing indicators
            window.Echo.private(`user.${{{ auth()->id() }}}`)
                .listen('.user.typing', (e) => {
                    if (e.user_id === this.otherUserId) {
                        this.isTyping = true;
                        clearTimeout(this.typingTimeout);
                        this.typingTimeout = setTimeout(() => {
                            this.isTyping = false;
                        }, 3000);
                    }
                });

            // Listen for presence updates
            if (this.domainId) {
                window.Echo.join(`auction.${this.domainId}`)
                    .here((users) => {
                        this.isOnline = users.some(user => user.id === this.otherUserId);
                    })
                    .joining((user) => {
                        if (user.id === this.otherUserId) {
                            this.isOnline = true;
                        }
                    })
                    .leaving((user) => {
                        if (user.id === this.otherUserId) {
                            this.isOnline = false;
                        }
                    });
            }
        },

        async loadMessages() {
            try {
                const response = await fetch(`/api/messages/conversations/${this.otherUserId}${this.domainId ? `?domain_id=${this.domainId}` : ''}`);
                const data = await response.json();
                this.messages = data.messages.reverse();
                this.$nextTick(() => this.scrollToBottom());
            } catch (error) {
                console.error('Failed to load messages:', error);
            }
        },

        async loadOtherUser() {
            try {
                const response = await fetch(`/api/users/${this.otherUserId}`);
                const data = await response.json();
                this.otherUser = data.user;
            } catch (error) {
                console.error('Failed to load user:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.isLoading) return;

            const message = this.newMessage.trim();
            this.newMessage = '';
            this.isLoading = true;

            try {
                const response = await fetch('/api/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                    },
                    body: JSON.stringify({
                        to_user_id: this.otherUserId,
                        domain_id: this.domainId,
                        body: message,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Failed to send message');
                }

                const data = await response.json();
                this.messages.push(data.message);
                this.scrollToBottom();
            } catch (error) {
                console.error('Failed to send message:', error);
                this.newMessage = message; // Restore message on error
            } finally {
                this.isLoading = false;
            }
        },

        async markAsRead(messageId) {
            try {
                await fetch(`/api/messages/${messageId}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                    },
                });
            } catch (error) {
                console.error('Failed to mark message as read:', error);
            }
        },

        handleTyping() {
            // Implement typing indicator if needed
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            });
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        open() {
            this.isOpen = true;
            this.isMinimized = false;
        },

        close() {
            this.isOpen = false;
        },

        toggleMinimize() {
            this.isMinimized = !this.isMinimized;
        },
    }
}
</script>
