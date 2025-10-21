@extends('layouts.app')

@section('title', 'Buyer Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Buyer Dashboard</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Track your bids, offers, and purchased domains</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Bids</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_bids'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Winning Bids</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['winning_bids'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-yellow-500 to-orange-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Watchlist</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['watchlist_count'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_spent'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <a href="{{ route('buyer.dashboard', ['tab' => 'won']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'won' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        Won Domains
                    </a>
                    <a href="{{ route('buyer.dashboard', ['tab' => 'bids']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'bids' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        All Bids
                    </a>
                    <a href="{{ route('buyer.dashboard', ['tab' => 'offers']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'offers' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        Offers
                    </a>
                    <a href="{{ route('buyer.dashboard', ['tab' => 'watching']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'watching' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        Watching
                    </a>
                    <a href="{{ route('buyer.dashboard', ['tab' => 'messages']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ $tab === 'messages' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        Messages
                    </a>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                @switch($tab)
                    @case('won')
                        @include('dashboard.partials.buyer-won', ['bids' => $data])
                        @break
                    @case('bids')
                        @include('dashboard.partials.buyer-bids', ['bids' => $data])
                        @break
                    @case('offers')
                        @include('dashboard.partials.buyer-offers', ['offers' => $data])
                        @break
                    @case('watching')
                        @include('dashboard.partials.buyer-watching', ['watchlist' => $data])
                        @break
                    @case('messages')
                        @include('dashboard.partials.buyer-messages', ['conversations' => $data])
                        @break
                @endswitch
            </div>
        </div>
    </div>
</div>
@endsection
