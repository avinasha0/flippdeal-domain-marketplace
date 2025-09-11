@extends('layouts.app')

@section('title', 'Buy & Sell Domains & Websites')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Flip <span class="text-yellow-300">Domains</span> & <span class="text-yellow-300">Websites</span> for Profit
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-4xl mx-auto">
                The premier marketplace for buying and selling digital assets. Turn your investments into profits with our secure platform.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#marketplace" class="inline-flex items-center px-8 py-4 text-lg font-medium text-blue-600 bg-white hover:bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Browse Marketplace
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-white/10 backdrop-blur-sm border-2 border-white/30 hover:bg-white/20 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-white/10 backdrop-blur-sm border-2 border-white/30 hover:bg-white/20 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Start Flipping
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-gray-50 dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 text-center hover:shadow-2xl transition-all duration-300">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">$2.5M+</div>
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Sales Volume</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 text-center hover:shadow-2xl transition-all duration-300">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">5,000+</div>
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Active Listings</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 text-center hover:shadow-2xl transition-all duration-300">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">98%</div>
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Success Rate</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 text-center hover:shadow-2xl transition-all duration-300">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">24/7</div>
                <div class="text-gray-600 dark:text-gray-400 text-sm font-medium">Support Available</div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="py-24 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Why Choose FlippDeal?
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Professional platform designed for serious domain and website flippers
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Secure Escrow</h3>
                <p class="text-gray-600 dark:text-gray-400">Safe transactions with our trusted escrow service. Your money is protected until you're satisfied.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Instant Transfer</h3>
                <p class="text-gray-600 dark:text-gray-400">Quick domain and website transfers. Get your assets in minutes, not days.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Analytics & Insights</h3>
                <p class="text-gray-600 dark:text-gray-400">Detailed analytics to help you make informed decisions about your investments.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Verification System</h3>
                <p class="text-gray-600 dark:text-gray-400">All listings are verified for authenticity. Buy with confidence.</p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-red-100 dark:bg-red-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">24/7 Support</h3>
                <p class="text-gray-600 dark:text-gray-400">Round-the-clock customer support to help you with any questions or issues.</p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Low Fees</h3>
                <p class="text-gray-600 dark:text-gray-400">Competitive fees that maximize your profits. Keep more of what you earn.</p>
            </div>
        </div>
    </div>
</div>

<!-- Marketplace Preview Section -->
<div id="marketplace" class="py-24 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Featured Listings
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Discover premium domains and websites ready for flipping
            </p>
            
            <!-- Info Message for Unregistered Users -->
            @guest
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg max-w-2xl mx-auto">
                    <div class="flex items-center justify-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Note:</strong> Login required to view domain details. 
                                <a href="{{ route('login') }}" class="font-medium underline hover:text-blue-900 dark:hover:text-blue-100">Login</a> or 
                                <a href="{{ route('register') }}" class="font-medium underline hover:text-blue-900 dark:hover:text-blue-100">Register</a>.
                            </p>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
            
        @if(isset($publishedDomains) && $publishedDomains && $publishedDomains->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($publishedDomains as $domain)
                    @if($domain && $domain->user)
                        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 text-xs font-medium px-3 py-1 rounded-full">
                                        {{ $domain->category ?? 'Domain' }}
                                    </span>
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $domain->formatted_price }}</span>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $domain->full_domain }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                    {{ $domain->description ? Str::limit($domain->description, 80) : 'Premium domain available for sale' }}
                                </p>
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <span>Listed by {{ $domain->user->name }}</span>
                                    <span>{{ $domain->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if($domain->has_website)
                                        <span class="bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 text-xs font-medium px-2 py-1 rounded-full">
                                            Includes Website
                                        </span>
                                    @endif
                                    @if($domain->has_traffic)
                                        <span class="bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300 text-xs font-medium px-2 py-1 rounded-full">
                                            Has Traffic
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
                    @endif
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('domains.public.index') }}" class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    View All Listings
                </a>
            </div>
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
</div>

<!-- How It Works Section -->
<div id="how-it-works" class="py-24 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                How FlippDeal Works
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Simple 3-step process to start flipping domains and websites
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">1</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">List Your Asset</h3>
                <p class="text-gray-600 dark:text-gray-400">Create a detailed listing for your domain or website. Include analytics, revenue data, and growth potential.</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">2</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Get Offers</h3>
                <p class="text-gray-600 dark:text-gray-400">Receive offers from interested buyers. Negotiate terms and find the best deal for your asset.</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">3</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Close & Transfer</h3>
                <p class="text-gray-600 dark:text-gray-400">Use our secure escrow service to complete the transaction. Get paid and transfer ownership safely.</p>
            </div>
        </div>
    </div>
</div>

<!-- Success Stories Section -->
<div class="py-24 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Success Stories
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                Real flippers making real profits on our platform
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">JS</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">John Smith</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Domain Flipper</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"Bought cryptotrader.com for $15K, sold for $45K in 6 months. FlippDeal made the process seamless!"</p>
                <div class="text-sm text-green-600 dark:text-green-400 font-semibold">+200% ROI</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">MJ</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">Maria Johnson</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Website Flipper</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"Flipped my first website for $25K profit. The escrow service gave me confidence to trade safely."</p>
                <div class="text-sm text-green-600 dark:text-green-400 font-semibold">+150% ROI</div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-xl border border-gray-200 dark:border-gray-700 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white font-bold">DL</span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">David Lee</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Portfolio Investor</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">"Built a portfolio of 50 domains through FlippDeal. Consistent returns and excellent support team."</p>
                <div class="text-sm text-green-600 dark:text-green-400 font-semibold">+300% ROI</div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Ready to Start Flipping?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Join thousands of successful flippers making profits on our platform
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-8 py-4 text-lg font-medium text-blue-600 bg-white hover:bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 text-lg font-medium text-blue-600 bg-white hover:bg-gray-50 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Start Flipping Today
                </a>
            @endauth
            <a href="#marketplace" class="inline-flex items-center px-8 py-4 text-lg font-medium text-white bg-white/10 backdrop-blur-sm border-2 border-white/30 hover:bg-white/20 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Browse Marketplace
            </a>
        </div>
    </div>
</div>
@endsection
