@extends('layouts.app')

@section('title', 'Conversation - ' . $conversation->domain->full_domain)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('conversations.index') }}" 
                       class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Messages
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        About: <a href="{{ route('domains.show', $conversation->domain) }}" 
                                  class="text-purple-600 dark:text-purple-400 hover:underline">
                            {{ $conversation->domain->full_domain }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Header -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 mb-6">
            <div class="flex items-center space-x-4">
                <img class="h-16 w-16 rounded-full object-cover" 
                     src="{{ $otherUser->avatar_url }}" 
                     alt="{{ $otherUser->name }}">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $otherUser->name }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $otherUser->email }}
                    </p>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <span>Domain: {{ $conversation->domain->full_domain }}</span>
                        <span>•</span>
                        <span>Price: {{ $conversation->domain->formatted_price }}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('domains.show', $conversation->domain) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        View Domain
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            <!-- Messages List -->
            <div id="messages-container" class="h-96 overflow-y-auto p-6 space-y-4">
                @foreach($conversation->messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end space-x-2 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <img class="h-8 w-8 rounded-full object-cover flex-shrink-0" 
                                     src="{{ $message->sender->avatar_url }}" 
                                     alt="{{ $message->sender->name }}">
                                <div class="flex flex-col {{ $message->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
                                    <div class="px-4 py-2 rounded-2xl {{ $message->sender_id === auth()->id() 
                                        ? 'bg-gradient-to-r from-purple-600 to-blue-600 text-white' 
                                        : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                                        <p class="text-sm">{{ $message->message }}</p>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $message->created_at->format('M j, Y g:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                <form id="message-form" class="flex space-x-4">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <div class="flex-1">
                        <textarea name="message" 
                                  id="message-input"
                                  rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                                  placeholder="Type your message..."
                                  required></textarea>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" 
                                id="send-button"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const messagesContainer = document.getElementById('messages-container');

    // Auto-scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Initial scroll to bottom
    scrollToBottom();

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable form
        sendButton.disabled = true;
        sendButton.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Sending...';

        // Send message via AJAX
        fetch('{{ route("messages.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conversation_id: {{ $conversation->id }},
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI
                addMessageToUI(data.message);
                messageInput.value = '';
                scrollToBottom();
            } else {
                alert('Failed to send message. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            // Re-enable form
            sendButton.disabled = false;
            sendButton.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>Send';
        });
    });

    // Add message to UI
    function addMessageToUI(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex justify-end';
        
        const now = new Date();
        const timeString = now.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });

        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="flex items-end space-x-2 flex-row-reverse space-x-reverse">
                    <img class="h-8 w-8 rounded-full object-cover flex-shrink-0" 
                         src="${message.sender.avatar_url}" 
                         alt="${message.sender.name}">
                    <div class="flex flex-col items-end">
                        <div class="px-4 py-2 rounded-2xl bg-gradient-to-r from-purple-600 to-blue-600 text-white">
                            <p class="text-sm">${message.message}</p>
                        </div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            ${timeString}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
    }

    // Auto-focus message input
    messageInput.focus();
});
</script>
@endsection
