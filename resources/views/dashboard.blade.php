@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                Welcome Back, {{ ucfirst(auth()->user()->name) }}!
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
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>'
            color="blue"
        />

        <!-- Active Listings -->
        <x-dashboard.card 
            title="Active Listings" 
            value="{{ $stats['active_listings'] ?? 0 }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>'
            color="green"
        />

        <!-- Total Bids -->
        <x-dashboard.card 
            title="Bids Received" 
            value="{{ $stats['total_bids'] ?? 0 }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'
            color="yellow"
        />

        <!-- Earnings -->
        <x-dashboard.card 
            title="Total Earnings" 
            value="${{ number_format($stats['total_earnings'] ?? 0, 2) }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
            color="purple"
        />
                                </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Draft Domains -->
        <x-dashboard.card 
            title="Draft Listings" 
            value="{{ $stats['draft_domains'] ?? 0 }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/></svg>'
            color="yellow"
        />

        <!-- Sold Domains -->
        <x-dashboard.card 
            title="Domains Sold" 
            value="{{ $stats['sold_domains'] ?? 0 }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>'
            color="green"
        />

        <!-- Active Auctions -->
        <x-dashboard.card 
            title="Active Auctions" 
            value="{{ $stats['active_auctions'] ?? 0 }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
            color="indigo"
        />

        <!-- Wallet Balance -->
        <x-dashboard.card 
            title="Wallet Balance" 
            value="${{ number_format($stats['wallet_balance'] ?? 0, 2) }}"
            icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
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
                                        @php
                                            $extension = $domain->domain_extension ?? 'com';
                                            $iconClass = match($extension) {
                                                'com' => 'from-blue-500 to-blue-600',
                                                'org' => 'from-green-500 to-green-600',
                                                'net' => 'from-purple-500 to-purple-600',
                                                'io' => 'from-orange-500 to-orange-600',
                                                'co' => 'from-red-500 to-red-600',
                                                'ai' => 'from-indigo-500 to-indigo-600',
                                                'app' => 'from-pink-500 to-pink-600',
                                                'dev' => 'from-cyan-500 to-cyan-600',
                                                'tech' => 'from-teal-500 to-teal-600',
                                                'online' => 'from-yellow-500 to-yellow-600',
                                                default => 'from-gray-500 to-gray-600'
                                            };
                                        @endphp
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $iconClass }} rounded-xl flex items-center justify-center shadow-lg">
                                            @if($extension === 'com')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                            @elseif($extension === 'org')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @elseif($extension === 'net')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                </svg>
                                            @elseif($extension === 'io')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $domain->full_domain ?? 'example.com' }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @if(($domain->status ?? 'active') === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Active
                                                </span>
                                            @elseif($domain->status === 'sold')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    Sold
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
                            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                            </div>
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
                                        @php
                                            $extension = $domain->domain_extension ?? 'com';
                                            $iconClass = match($extension) {
                                                'com' => 'from-blue-500 to-blue-600',
                                                'org' => 'from-green-500 to-green-600',
                                                'net' => 'from-purple-500 to-purple-600',
                                                'io' => 'from-orange-500 to-orange-600',
                                                'co' => 'from-red-500 to-red-600',
                                                'ai' => 'from-indigo-500 to-indigo-600',
                                                'app' => 'from-pink-500 to-pink-600',
                                                'dev' => 'from-cyan-500 to-cyan-600',
                                                'tech' => 'from-teal-500 to-teal-600',
                                                'online' => 'from-yellow-500 to-yellow-600',
                                                default => 'from-gray-500 to-gray-600'
                                            };
                                        @endphp
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $iconClass }} rounded-xl flex items-center justify-center shadow-lg">
                                            @if($extension === 'com')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                            @elseif($extension === 'org')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @elseif($extension === 'net')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                </svg>
                                            @elseif($extension === 'io')
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                                </svg>
                                            @else
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $domain->full_domain }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Draft
                                                </span>
                                                @if(!$domain->domain_verified)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        Verify Required
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Verified
                                                    </span>
                                                @endif
                                            </div>
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
                                        @if($domain->status === 'draft')
                                            @if(!$domain->domain_verified)
                                                <a href="{{ route('domains.verification', $domain) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                    </svg>
                                                    Verify
                                                </a>
                                            @else
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
                                            @endif
                                        @elseif($domain->status === 'sold')
                                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded cursor-not-allowed">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Sold
                                            </span>
                                        @elseif($domain->status === 'active')
                                            @if(!$domain->hasPendingActions())
                                                <form method="POST" action="{{ route('domains.change-to-draft', $domain) }}" class="inline" onsubmit="return confirm('Are you sure you want to change this domain to draft status? This will make it invisible to buyers.')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        To Draft
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded cursor-not-allowed" title="Cannot change to draft: {{ implode(', ', $domain->getPendingActionsSummary()) }}">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        @elseif($domain->status === 'inactive')
                                            @if(!$domain->hasPendingActions())
                                                <form method="POST" action="{{ route('domains.change-to-draft', $domain) }}" class="inline" onsubmit="return confirm('Are you sure you want to change this domain to draft status? This will make it invisible to buyers.')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        To Draft
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded cursor-not-allowed" title="Cannot change to draft: {{ implode(', ', $domain->getPendingActionsSummary()) }}">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        @endif
                                        
                                        @if(in_array($domain->status, ['draft', 'inactive']))
                                            <form method="POST" action="{{ route('domains.destroy', $domain) }}" class="inline" onsubmit="return confirm('⚠️ WARNING: This will permanently delete your domain listing.\n\nThis action cannot be undone and will remove:\n• All domain information\n• Any associated data\n\nAre you absolutely sure you want to delete this domain?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
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
                        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>'
                        color="blue"
                    />

                    <x-dashboard.quick-action 
                        title="Browse Domains"
                        description="Find domains to buy"
                        href="{{ route('domains.public.index') }}"
                        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>'
                        color="green"
                    />

                    <x-dashboard.quick-action 
                        title="Verify Profile"
                        description="Complete verification"
                        href="{{ route('verification.index') }}"
                        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
                        color="purple"
                    />

                    <x-dashboard.quick-action 
                        title="View Orders"
                        description="Track your purchases"
                        href="{{ route('orders.index') }}"
                        icon='<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M7 4V2C7 1.45 7.45 1 8 1H16C16.55 1 17 1.45 17 2V4H20C20.55 4 21 4.45 21 5S20.55 6 20 6H19V19C19 20.1 18.1 21 17 21H7C5.9 21 5 20.1 5 19V6H4C3.45 6 3 5.55 3 5S3.45 4 4 4H7ZM9 3V4H15V3H9ZM7 6V19H17V6H7Z"/></svg>'
                        color="indigo"
                    />
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                    <button 
                        id="refresh-activity" 
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                        onclick="refreshActivity()"
                    >
                        Refresh
                    </button>
                </div>
                <div id="activity-container" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Activity items will be loaded here dynamically -->
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="mt-2">Loading activities...</p>
                    </div>
                </div>
            </div>

            <!-- Wallet & Earnings -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Wallet & Earnings</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['wallet_balance'] ?? 0, 2) }}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Available Balance</p>
                    </div>
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('wallet.index') }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 inline-block text-center">
                            Manage Wallet
                        </a>
                        <a href="{{ route('wallet.index') }}" class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-3 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 inline-block text-center">
                            View Transaction History
                        </a>
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

<script>
// Simple refresh function for the button
function refreshActivity() {
    if (window.activityManager) {
        window.activityManager.refresh();
    }
}
</script>
@endsection