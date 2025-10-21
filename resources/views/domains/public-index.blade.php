@extends('layouts.app')

@section('title', 'Browse Domains')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:text-4xl sm:truncate">
                        Browse Domain Listings
                    </h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                        Discover premium domains and websites ready for flipping. Find your next investment opportunity.
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            @auth
                <a href="{{ route('domains.create') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    List Your Domain
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Start Selling
                </a>
            @endauth
        </div>
    </div>

    <!-- Info Message for Unregistered Users -->
    @guest
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <strong>Note:</strong> You need to login to view detailed information about domains. 
                        <a href="{{ route('login') }}" class="font-medium underline hover:text-blue-900 dark:hover:text-blue-100">Login here</a> or 
                        <a href="{{ route('register') }}" class="font-medium underline hover:text-blue-900 dark:hover:text-blue-100">register for free</a>.
                    </p>
                </div>
            </div>
        </div>
    @endguest
            
    @if($domains->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($domains as $domain)
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 text-xs font-medium px-3 py-1 rounded-full">
                                {{ $domain->category ?? 'Domain' }}
                            </span>
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $domain->formatted_price }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $domain->full_domain }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            {{ $domain->description ? Str::limit($domain->description, 80) : 'Premium domain available for sale' }}
                        </p>
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span>Listed by {{ $domain->user ? $domain->user->name : 'Unknown User' }}</span>
                            <span>{{ $domain->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <!-- Domain Features -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($domain->has_website)
                                <span class="bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                    Website
                                </span>
                            @endif
                            @if($domain->has_traffic)
                                <span class="bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300 text-xs font-medium px-2 py-1 rounded-full">
                                    Traffic
                                </span>
                            @endif
                            @if($domain->premium_domain)
                                <span class="bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 text-xs font-medium px-2 py-1 rounded-full">
                                    Premium
                                </span>
                            @endif
                        </div>
                        
                        @auth
                            <a href="{{ route('domains.show', $domain) }}" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-center font-medium">
                                View Details
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 text-center font-medium">
                                Login to View Details
                            </a>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($domains->hasPages())
            <div class="mt-8">
                {{ $domains->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-32 h-32 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center mb-8">
                <svg class="w-16 h-16 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">No published listings yet</h3>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                Be the first to publish a domain listing and start your flipping journey!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('domains.create') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Listing
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Register to List
                    </a>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
