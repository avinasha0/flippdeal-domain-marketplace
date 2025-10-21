@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Stay updated with your domain marketplace activity</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="mark-all-read" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Mark All Read
                    </button>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $notifications->total() }} notifications
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            @if($notifications->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($notifications as $notification)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <div class="flex items-start space-x-4">
                                <!-- Notification Icon -->
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $notification->is_read ? 'bg-gray-100 dark:bg-gray-700' : 'bg-blue-100 dark:bg-blue-900' }}">
                                        @switch($notification->type)
                                            @case('bid_received')
                                                <svg class="h-5 w-5 {{ $notification->is_read ? 'text-gray-600 dark:text-gray-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                @break
                                            @case('offer_received')
                                                <svg class="h-5 w-5 {{ $notification->is_read ? 'text-gray-600 dark:text-gray-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                @break
                                            @case('message_received')
                                                <svg class="h-5 w-5 {{ $notification->is_read ? 'text-gray-600 dark:text-gray-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                @break
                                            @case('auction_won')
                                                <svg class="h-5 w-5 {{ $notification->is_read ? 'text-gray-600 dark:text-gray-400' : 'text-green-600 dark:text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                </svg>
                                                @break
                                            @default
                                                <svg class="h-5 w-5 {{ $notification->is_read ? 'text-gray-600 dark:text-gray-400' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828z"></path>
                                                </svg>
                                        @endswitch
                                    </div>
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $notification->title }}
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $notification->message }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if(!$notification->is_read)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    New
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    @if($notification->data && isset($notification->data['domain_id']))
                                        <div class="mt-3 flex items-center space-x-3">
                                            <a href="{{ route('domains.show', $notification->data['domain_id']) }}" 
                                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-200 dark:hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                                View Domain
                                            </a>
                                            @if(isset($notification->data['conversation_id']))
                                                <a href="{{ route('conversations.show', $notification->data['conversation_id']) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                    View Message
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" 
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                            Mark Read
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-600 text-xs font-medium rounded-md text-red-700 dark:text-red-300 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No notifications yet</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        You'll receive notifications for bids, offers, messages, and other important updates.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

document.getElementById('mark-all-read').addEventListener('click', function() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endsection
