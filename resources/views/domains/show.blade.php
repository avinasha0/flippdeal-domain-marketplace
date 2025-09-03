@extends('layouts.app')

@section('title', $domain->full_domain . ' - Domain Details')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight font-mono">
                {{ $domain->full_domain }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-4xl mx-auto">
                {{ $domain->description ?: 'Premium domain available for purchase' }}
            </p>
            
            <!-- Quick Stats -->
            <div class="flex justify-center space-x-8 mb-8">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $domain->category ?: 'General' }}</div>
                    <div class="text-blue-200 text-sm">Category</div>
                </div>
                @if($domain->registration_date)
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $domain->registration_date->format('Y') }}</div>
                    <div class="text-blue-200 text-sm">Registered</div>
                </div>
                @endif
                @if($domain->has_website)
                <div class="text-center">
                    <div class="text-2xl font-bold">✓</div>
                    <div class="text-blue-200 text-sm">Website</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Domain Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Domain Features Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    Domain Features
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($domain->has_website)
                    <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                        <div class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-green-900 dark:text-green-100">Active Website</h3>
                            <p class="text-sm text-green-700 dark:text-green-300">Domain comes with existing website</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($domain->has_traffic)
                    <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Traffic</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300">Domain receives regular traffic</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($domain->premium_domain)
                    <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-100">Premium</h3>
                            <p class="text-sm text-purple-700 dark:text-purple-300">High-value premium domain</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($domain->domain_verified)
                    <div class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-indigo-900 dark:text-indigo-100">Verified</h3>
                            <p class="text-sm text-indigo-700 dark:text-indigo-300">Domain ownership verified</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description Card -->
            @if($domain->description)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    About This Domain
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg">{{ $domain->description }}</p>
            </div>
            @endif

            <!-- Additional Features -->
            @if($domain->additional_features)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    Additional Features
                </h2>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $domain->additional_features }}</p>
            </div>
            @endif

            <!-- Domain Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Domain Timeline
                </h2>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Listed for Sale</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $domain->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($domain->registration_date)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Domain Registered</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $domain->registration_date->format('F j, Y') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($domain->expiry_date)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Expires</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $domain->expiry_date->format('F j, Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Action Cards -->
        <div class="space-y-6">
            <!-- Owner Actions (if authenticated and owner) -->
            @auth
                @if(auth()->id() === $domain->user_id)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all duration-300">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                            <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            Owner Actions
                        </h3>
                        
                        <div class="space-y-3">
                            @if($domain->status === 'draft')
                                <form method="POST" action="{{ route('domains.publish', $domain) }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Publish Domain
                                    </button>
                                </form>
                            @endif
                            
                            @if($domain->status === 'active')
                                <form method="POST" action="{{ route('domains.mark-sold', $domain) }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Mark as Sold
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('domains.deactivate', $domain) }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Deactivate
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('domains.edit', $domain) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Domain
                            </a>
                            
                            <a href="{{ route('my.domains.index') }}" class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Domains
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Buyer Actions -->
                    <div class="space-y-6">
                        <!-- Buy It Now Option -->
                        @if($domain->bin_price)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-2 border-green-200 dark:border-green-800 hover:shadow-2xl transition-all duration-300">
                                <div class="text-center mb-4">
                                    <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-2">Buy It Now</h3>
                                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $domain->formatted_bin_price }}</div>
                                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">Purchase this domain immediately</p>
                                </div>
                                <form method="POST" action="{{ route('domains.buy', $domain) }}" class="w-full">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="stripe">
                                    <input type="hidden" name="purchase_type" value="bin_price">
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                        </svg>
                                        Buy Now
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Purchase at Asking Price -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-2 border-purple-200 dark:border-purple-800 hover:shadow-2xl transition-all duration-300">
                            <div class="text-center mb-4">
                                <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-2">Purchase at Asking Price</h3>
                                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $domain->formatted_price }}</div>
                                <p class="text-sm text-purple-700 dark:text-purple-300 mt-1">Buy this domain at the listed asking price</p>
                            </div>
                            <form method="POST" action="{{ route('domains.buy', $domain) }}" class="w-full">
                                @csrf
                                <input type="hidden" name="payment_method" value="stripe">
                                <input type="hidden" name="purchase_type" value="asking_price">
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-200">
                                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                    </svg>
                                    Purchase
                                </button>
                            </form>
                        </div>

                        <!-- Make an Offer Option -->
                        @if($domain->acceptsOffers())
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-2 border-blue-200 dark:border-blue-800 hover:shadow-2xl transition-all duration-300">
                                <div class="text-center mb-4">
                                    <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-2">Make an Offer</h3>
                                    @if($domain->minimum_offer)
                                        <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">Min: {{ $domain->formatted_minimum_offer }}</div>
                                    @else
                                        <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">No Minimum</div>
                                    @endif
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Submit an offer below the asking price</p>
                                </div>
                                <a href="{{ route('offers.create', ['domain_id' => $domain->id]) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Make Offer
                                </a>
                            </div>
                        @endif

                        <!-- Auction/Bidding Option -->
                        @if($domain->hasBidding())
                            @if($domain->isReadyForBidding())
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-2 border-orange-200 dark:border-orange-800 hover:shadow-2xl transition-all duration-300">
                                    <div class="text-center mb-4">
                                        <h3 class="text-xl font-bold text-orange-900 dark:text-orange-100 mb-2">
                                            @if($domain->auction_status === 'active')
                                                🚀 Auction Active
                                            @elseif($domain->auction_status === 'scheduled')
                                                ⏰ Auction Scheduled
                                            @elseif($domain->auction_status === 'ended')
                                                🏁 Auction Ended
                                            @else
                                                🎯 Auction
                                            @endif
                                        </h3>
                                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                                            {{ $domain->formatted_current_bid ?: $domain->formatted_starting_bid }}
                                        </div>
                                        <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">
                                            @if($domain->current_bid)
                                                Current Bid • {{ $domain->bid_count }} bids
                                            @else
                                                Starting Bid • No bids yet
                                            @endif
                                        </p>
                                        
                                        @if($domain->auction_status === 'active')
                                            <div class="mt-3 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                                <p class="text-sm text-orange-800 dark:text-orange-200 font-medium">
                                                    ⏰ Ends: {{ $domain->auction_time_remaining }}
                                                </p>
                                                @if($domain->reserve_price && !$domain->reserve_met)
                                                    <p class="text-xs text-orange-600 dark:text-orange-400 font-medium mt-1">⚠️ Reserve not met</p>
                                                @endif
                                            </div>
                                        @elseif($domain->auction_status === 'scheduled')
                                            <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                                                    📅 Starts: {{ $domain->auction_start ? $domain->auction_start->format('M j, Y g:i A') : 'TBD' }}
                                                </p>
                                                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                                                    🏁 Ends: {{ $domain->auction_end ? $domain->auction_end->format('M j, Y g:i A') : 'TBD' }}
                                                </p>
                                            </div>
                                        @elseif($domain->auction_status === 'ended')
                                            <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-medium">
                                                    🏁 Ended: {{ $domain->auction_end ? $domain->auction_end->format('M j, Y g:i A') : 'TBD' }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-3">
                                        @if($domain->auction_status === 'active')
                                            <a href="{{ route('domains.bids.create', $domain) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-4 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                Place Bid
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('domains.bids.index', $domain) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            @if($domain->auction_status === 'active')
                                                View Bid History
                                            @elseif($domain->auction_status === 'scheduled')
                                                View Auction Details
                                            @elseif($domain->auction_status === 'ended')
                                                View Final Results
                                            @else
                                                View Auction Info
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border-2 border-yellow-200 dark:border-yellow-800 hover:shadow-2xl transition-all duration-300">
                                    <div class="text-center mb-4">
                                        <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-2">⚠️ Auction Setup Required</h3>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Bidding is enabled but auction details need to be configured</p>
                                    </div>
                                    @if(auth()->id() === $domain->user_id)
                                        <a href="{{ route('domains.edit', $domain) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200 block text-center">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Complete Auction Setup
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            @else
                <!-- Non-authenticated users -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Login Required</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Please login to purchase or bid on this domain</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Login to Buy
                    </a>
                </div>
            @endauth

            <!-- Browse More Domains -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center hover:shadow-2xl transition-all duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Looking for More?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Discover other premium domains in our marketplace</p>
                <a href="{{ route('domains.public.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Browse Domains
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
