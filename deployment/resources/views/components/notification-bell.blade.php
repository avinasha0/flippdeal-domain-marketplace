@php
    $unreadCount = auth()->check() ? auth()->user()->unread_conversation_count : 0;
@endphp

<div x-data="notificationBell()" class="relative">
    <!-- Notification Bell Button -->
    <button @click="toggleNotifications()" 
            class="relative inline-flex items-center p-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[20px] h-5 flex items-center justify-center shadow-lg border-2 border-white dark:border-gray-800">
        </span>
    </button>

    <!-- Notifications Dropdown -->
    <div x-show="isOpen" 
         @click.away="isOpen = false" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 z-50 border border-gray-200 dark:border-gray-700">
        
        <!-- Header -->
        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Messages</h3>
            <a href="{{ route('conversations.index') }}" class="text-xs text-purple-600 dark:text-purple-400 hover:underline">View all</a>
        </div>

        <!-- Notifications List -->
        <div class="max-h-64 overflow-y-auto">
            <template x-for="notification in notifications" :key="notification.id">
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                     @click="markAsRead(notification.id); window.location.href = notification.url">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                                <span x-text="notification.sender.charAt(0)"></span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notification.title"></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notification.time"></p>
                        </div>
                        <div x-show="!notification.read" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Empty State -->
            <div x-show="notifications.length === 0" class="px-4 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No new messages</p>
            </div>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        isOpen: false,
        unreadCount: {{ $unreadCount }},
        notifications: [],
        
        init() {
            this.loadNotifications();
            this.setupEventListeners();
        },
        
        toggleNotifications() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },
        
        loadNotifications() {
            fetch('/api/notifications/recent', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    this.notifications = [];
                    this.unreadCount = 0;
                });
        },
        
        markAsRead(notificationId) {
            fetch(`/api/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    // Update the notification as read
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.read = true;
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        },
        
        setupEventListeners() {
            // Listen for new message events
            window.addEventListener('message-received', (event) => {
                this.unreadCount++;
                this.addNotification(event.detail);
            });
            
            // Listen for message read events
            window.addEventListener('message-read', (event) => {
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            });
        },
        
        addNotification(messageData) {
            const notification = {
                id: Date.now(),
                title: `New message from ${messageData.sender}`,
                message: messageData.message,
                sender: messageData.sender,
                time: 'Just now',
                read: false,
                url: `/conversations/${messageData.conversation_id}`
            };
            
            this.notifications.unshift(notification);
            
            // Keep only last 10 notifications
            if (this.notifications.length > 10) {
                this.notifications = this.notifications.slice(0, 10);
            }
        }
    }
}
</script>