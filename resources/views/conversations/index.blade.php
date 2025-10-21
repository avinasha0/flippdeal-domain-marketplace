@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Messages</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your conversations with buyers and sellers</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $conversations->total() }} conversations
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            @if($conversations->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($conversations as $conversation)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <div class="flex items-center space-x-4">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    @php
                                        $otherUser = $conversation->buyer_id === auth()->id() ? $conversation->seller : $conversation->buyer;
                                    @endphp
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center text-white font-semibold text-lg">
                                        {{ substr($otherUser->name, 0, 1) }}
                                    </div>
                                </div>

                                <!-- Conversation Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $otherUser->name }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                About: {{ $conversation->domain->full_domain }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($conversation->getUnreadCountForUser(auth()->id()) > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    {{ $conversation->getUnreadCountForUser(auth()->id()) }}
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'No messages' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if($conversation->latestMessage)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 truncate">
                                            {{ $conversation->latestMessage->message }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <div class="flex-shrink-0">
                                    <a href="{{ route('conversations.show', $conversation) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                        View Conversation
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $conversations->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No conversations yet</h3>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Start a conversation by contacting a domain owner or responding to inquiries.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('domains.public.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                            Browse Domains
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
