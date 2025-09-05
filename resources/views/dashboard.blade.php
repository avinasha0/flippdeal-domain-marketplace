@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                Welcome back, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Here's an overview of your domain marketplace activity.
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ route('domains.create') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                List Domain
            </a>
        </div>
    </div>

    <!-- Verification Status Alert -->
    @if(!auth()->user()->isFullyVerified())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20 dark:border-yellow-500">
            <div class="flex">
                                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                        <strong>Complete your verification</strong> to access all marketplace features and start selling domains.
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('verification.index') }}" class="text-sm font-medium text-yellow-700 underline hover:text-yellow-600 dark:text-yellow-200 dark:hover:text-yellow-300">
                            Verify your account now →
                        </a>
                    </div>
                </div>
                                    </div>
                                </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- My Domains -->
        <x-dashboard.card 
            title="My Domains" 
            value="{{ $stats['my_domains'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path></svg>'
            color="blue"
        />

        <!-- Active Listings -->
        <x-dashboard.card 
            title="Active Listings" 
            value="{{ $stats['active_listings'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="green"
        />

        <!-- Total Bids -->
        <x-dashboard.card 
            title="Bids Received" 
            value="{{ $stats['total_bids'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="yellow"
        />

        <!-- Earnings -->
        <x-dashboard.card 
            title="Total Earnings" 
            value="${{ number_format($stats['total_earnings'] ?? 0, 2) }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>'
            color="purple"
        />
                                </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Pending Domains -->
        <x-dashboard.card 
            title="Pending Approval" 
            value="{{ $stats['pending_domains'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="yellow"
        />

        <!-- Sold Domains -->
        <x-dashboard.card 
            title="Domains Sold" 
            value="{{ $stats['sold_domains'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="green"
        />

        <!-- Active Auctions -->
        <x-dashboard.card 
            title="Active Auctions" 
            value="{{ $stats['active_auctions'] ?? 0 }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            color="indigo"
        />

        <!-- Wallet Balance -->
        <x-dashboard.card 
            title="Wallet Balance" 
            value="${{ number_format($stats['wallet_balance'] ?? 0, 2) }}"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>'
            color="blue"
        />
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- My Domains List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">My Recent Domains</h3>
                    <a href="{{ route('my.domains.index') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentDomains ?? [] as $domain)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $domain->full_domain ?? 'example.com' }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($domain->status ?? 'active' === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Active
                                                </span>
                                            @elseif($domain->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                    {{ ucfirst($domain->status ?? 'Draft') }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                        </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($domain->bin_price ?? 1500, 2) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $domain->created_at ? $domain->created_at->diffForHumans() : '2 days ago' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No domains yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by listing your first domain.</p>
                            <div class="mt-6">
                                <a href="{{ route('domains.create') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    List Domain
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Draft Listings Section -->
        @if(($draftDomains ?? [])->count() > 0)
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Draft Listings</h3>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            {{ $draftDomains->count() }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(!auth()->user()->isFullyVerified())
                        <span class="text-xs text-yellow-600 dark:text-yellow-400">Complete verification to publish</span>
                        @endif
                        <a href="{{ route('my.domains.index', ['status' => 'draft']) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($draftDomains as $domain)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $domain->full_domain }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Draft
                                            </span>
                                            @if($domain->enable_buy_now)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    Buy Now
                                                </span>
                                            @endif
                                            @if($domain->enable_bidding)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Bidding
                                                </span>
                                            @endif
                                            @if($domain->enable_offers)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    Offers
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($domain->asking_price, 2) }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $domain->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if(auth()->user()->isFullyVerified())
                                        <a href="{{ route('domains.edit', $domain) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('domains.publish', $domain) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Publish
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Complete verification to publish</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <x-dashboard.quick-action 
                        title="List New Domain"
                        description="Add a domain to sell"
                        href="{{ route('domains.create') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>'
                        color="blue"
                    />

                    <x-dashboard.quick-action 
                        title="Browse Domains"
                        description="Find domains to buy"
                        href="{{ route('domains.public.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
                        color="green"
                    />

                    <x-dashboard.quick-action 
                        title="Verify Profile"
                        description="Complete verification"
                        href="{{ route('verification.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        color="purple"
                    />

                    <x-dashboard.quick-action 
                        title="View Orders"
                        description="Track your purchases"
                        href="{{ route('orders.index') }}"
                        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>'
                        color="indigo"
                    />
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <x-dashboard.notification-item 
                        title="New Bid Received"
                        message="Someone bid $1,200 on your domain example.com"
                        time="2 hours ago"
                        type="success"
                        unread="true"
                    />

                    <x-dashboard.notification-item 
                        title="Domain Approved"
                        message="Your domain listing has been approved"
                        time="1 day ago"
                        type="success"
                    />

                    <x-dashboard.notification-item 
                        title="Auction Ending Soon"
                        message="Your auction for premium.com ends in 2 hours"
                        time="3 days ago"
                        type="warning"
                    />

                    <x-dashboard.notification-item 
                        title="Payment Received"
                        message="You received $1,500 for domain sale"
                        time="1 week ago"
                        type="success"
                    />
                </div>
            </div>

            <!-- Wallet & Earnings -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Wallet & Earnings</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['wallet_balance'] ?? 1250.50, 2) }}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Available Balance</p>
                    </div>
                    <div class="mt-6 space-y-3">
                        <button class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Withdraw Funds
                        </button>
                        <button class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-3 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            View Transaction History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Auctions Section -->
    @if(($activeAuctions ?? [])->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Active Auctions</h3>
                                                                    </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($activeAuctions as $auction)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $auction->full_domain ?? 'example.com' }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Live
                                                                    </span>
                                                                </div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($auction->current_bid ?? 1200, 2) }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $auction->bid_count ?? 5 }} bids
                                        </div>
                            <div class="mt-3">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                    <span x-data="{ timeLeft: '{{ $auction->auction_end ? $auction->auction_end->diffForHumans() : '2 hours' }}' }" x-text="timeLeft"></span>
                                        </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection