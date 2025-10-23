@extends('layouts.app')

@section('title', 'List Domain for Sale')
@section('description', 'List your domain for sale on FlippDeal marketplace. Create detailed listings with pricing, features, and verification to maximize your domain value.')
@section('keywords', 'list domain, sell domain, domain listing, create listing, domain for sale, marketplace listing, domain seller')

@section('content')
                        <!-- Page Header -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">List Domain for Sale</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Sell your domain and reach thousands of potential buyers.</p>
            
            <!-- Pricing Guidelines -->
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800">Pricing Guidelines:</h3>
                <ul class="text-xs text-blue-700 mt-1 list-disc list-inside">
                    <li><strong>Asking Price:</strong> Your desired selling price</li>
                    <li><strong>Buy Now:</strong> Optional - immediate purchase price (can be same as asking price)</li>
                    <li><strong>Offers:</strong> Set minimum/maximum offer ranges</li>
                    <li><strong>Bidding:</strong> Set starting bid and optional reserve price</li>
                    <li>All prices must be at least $0.01</li>
                </ul>
            </div>
                                </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>

                        <!-- Domain Listing Form -->
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
    <div class="px-6 py-8">
                                <!-- General Error Messages -->
                                @if ($errors->any())
                                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                        </div>
                                        <ul class="text-sm text-red-700 list-disc list-inside">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Success Messages -->
                                @if (session('success'))
                                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span class="text-green-800 font-medium">{{ session('success') }}</span>
                                        </div>
                                    </div>
                                @endif

                                <form method="post" action="{{ route('domains.store') }}" class="space-y-8" id="domain-form">
                                    @csrf

                                    <!-- Domain Information -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                        </svg>
                    </div>
                    Domain Information
                </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                        <label for="domain_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain Name <span class="text-red-500">*</span></label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" name="domain_name" id="domain_name" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('domain_name') border-red-500 @enderror" placeholder="example" value="{{ old('domain_name') }}" required>
                                                </div>
                                                @error('domain_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                        <label for="domain_extension" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Extension <span class="text-red-500">*</span></label>
                        <select name="domain_extension" id="domain_extension" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('domain_extension') border-red-500 @enderror">
                                                    <option value="">Select Extension</option>
                                                    <option value=".com" {{ old('domain_extension') == '.com' ? 'selected' : '' }}>.com</option>
                                                    <option value=".net" {{ old('domain_extension') == '.net' ? 'selected' : '' }}>.net</option>
                                                    <option value=".org" {{ old('domain_extension') == '.org' ? 'selected' : '' }}>.org</option>
                                                    <option value=".io" {{ old('domain_extension') == '.io' ? 'selected' : '' }}>.io</option>
                                                    <option value=".co" {{ old('domain_extension') == '.co' ? 'selected' : '' }}>.co</option>
                                                    <option value=".ai" {{ old('domain_extension') == '.ai' ? 'selected' : '' }}>.ai</option>
                                                    <option value=".app" {{ old('domain_extension') == '.app' ? 'selected' : '' }}>.app</option>
                                                    <option value=".dev" {{ old('domain_extension') == '.dev' ? 'selected' : '' }}>.dev</option>
                                                    <option value=".tech" {{ old('domain_extension') == '.tech' ? 'selected' : '' }}>.tech</option>
                                                    <option value=".store" {{ old('domain_extension') == '.store' ? 'selected' : '' }}>.store</option>
                                                    <option value=".shop" {{ old('domain_extension') == '.shop' ? 'selected' : '' }}>.shop</option>
                                                    <option value=".blog" {{ old('domain_extension') == '.blog' ? 'selected' : '' }}>.blog</option>
                                                    <option value=".news" {{ old('domain_extension') == '.news' ? 'selected' : '' }}>.news</option>
                                                    <option value=".info" {{ old('domain_extension') == '.info' ? 'selected' : '' }}>.info</option>
                                                    <option value=".biz" {{ old('domain_extension') == '.biz' ? 'selected' : '' }}>.biz</option>
                                                    <option value=".me" {{ old('domain_extension') == '.me' ? 'selected' : '' }}>.me</option>
                                                    <option value=".tv" {{ old('domain_extension') == '.tv' ? 'selected' : '' }}>.tv</option>
                                                    <option value=".cc" {{ old('domain_extension') == '.cc' ? 'selected' : '' }}>.cc</option>
                                                    <option value=".ws" {{ old('domain_extension') == '.ws' ? 'selected' : '' }}>.ws</option>
                                                    <option value=".xyz" {{ old('domain_extension') == '.xyz' ? 'selected' : '' }}>.xyz</option>
                                                </select>
                                                @error('domain_extension')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing & Category -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    Pricing & Category
                </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                        <label for="asking_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asking Price ($) <span class="text-red-500">*</span></label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                    </div>
                            <input type="number" name="asking_price" id="asking_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('asking_price') border-red-500 @enderror" placeholder="1000" min="1" step="0.01" value="{{ old('asking_price') }}" required>
                                                </div>
                                                @error('asking_price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select name="category" id="category" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('category') border-red-500 @enderror">
                                                    <option value="">Select Category</option>
                                                    <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business</option>
                                                    <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                                                    <option value="finance" {{ old('category') == 'finance' ? 'selected' : '' }}>Finance</option>
                                                    <option value="health" {{ old('category') == 'health' ? 'selected' : '' }}>Health & Fitness</option>
                                                    <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                                    <option value="entertainment" {{ old('category') == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                                    <option value="sports" {{ old('category') == 'sports' ? 'selected' : '' }}>Sports</option>
                                                    <option value="travel" {{ old('category') == 'travel' ? 'selected' : '' }}>Travel</option>
                                                    <option value="food" {{ old('category') == 'food' ? 'selected' : '' }}>Food & Dining</option>
                                                    <option value="fashion" {{ old('category') == 'fashion' ? 'selected' : '' }}>Fashion & Beauty</option>
                                                    <option value="real-estate" {{ old('category') == 'real-estate' ? 'selected' : '' }}>Real Estate</option>
                                                    <option value="automotive" {{ old('category') == 'automotive' ? 'selected' : '' }}>Automotive</option>
                                                    <option value="gaming" {{ old('category') == 'gaming' ? 'selected' : '' }}>Gaming</option>
                                                    <option value="crypto" {{ old('category') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sale Options -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    Sale Options
                </h3>
                                        <div class="space-y-6">
                                            <!-- Buy Now Option -->
                                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="enable_buy_now" id="enable_buy_now" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('enable_buy_now') ? 'checked' : '' }}>
                                                        <label for="enable_buy_now" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Buy Now</label>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Instant purchase option</span>
                                                </div>
                                                <div id="buy_now_fields" class="space-y-4" style="display: none;">
                                                    <div>
                                                        <label for="buy_now_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buy Now Price ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="buy_now_price" id="buy_now_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="Can be same as asking price" min="0.01" step="0.01" value="{{ old('buy_now_price') }}">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="buy_now_expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buy Now Expires (Optional)</label>
                                                        <input type="datetime-local" name="buy_now_expires_at" id="buy_now_expires_at" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" value="{{ old('buy_now_expires_at') }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Bidding Option -->
                                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="enable_bidding" id="enable_bidding" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('enable_bidding') ? 'checked' : '' }}>
                                                        <label for="enable_bidding" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Bidding/Auction</label>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Allow users to bid</span>
                                                </div>
                                                <div id="bidding_fields" class="space-y-4" style="display: none;">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="starting_bid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Starting Bid ($)</label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" name="starting_bid" id="starting_bid" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="100" min="1" step="0.01" value="{{ old('starting_bid') }}">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="reserve_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reserve Price ($)</label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" name="reserve_price" id="reserve_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="500" min="1" step="0.01" value="{{ old('reserve_price') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="auction_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auction Start</label>
                                                            <input type="datetime-local" name="auction_start" id="auction_start" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" value="{{ old('auction_start', now()->format('Y-m-d\TH:i')) }}">
                                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Defaults to current date/time</p>
                                                        </div>
                                                        <div>
                                                            <label for="auction_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auction End</label>
                                                            <input type="datetime-local" name="auction_end" id="auction_end" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" value="{{ old('auction_end', now()->addDays(90)->format('Y-m-d\TH:i')) }}">
                                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Defaults to 90 days from now</p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="minimum_bid_increment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Bid Increment ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="minimum_bid_increment" id="minimum_bid_increment" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="10" min="1" step="0.01" value="{{ old('minimum_bid_increment', 10) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Make An Offer Option -->
                                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="enable_offers" id="enable_offers" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('enable_offers') ? 'checked' : '' }}>
                                                        <label for="enable_offers" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Enable Make An Offer</label>
                                                    </div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Allow users to make offers</span>
                                                </div>
                                                <div id="offer_fields" class="space-y-4" style="display: none;">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="minimum_offer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Offer ($)</label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" name="minimum_offer" id="minimum_offer" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="200" min="1" step="0.01" value="{{ old('minimum_offer') }}">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label for="maximum_offer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Offer ($)</label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" name="maximum_offer" id="maximum_offer" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="2000" min="1" step="0.01" value="{{ old('maximum_offer') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" name="auto_accept_offers" id="auto_accept_offers" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('auto_accept_offers') ? 'checked' : '' }}>
                                                            <label for="auto_accept_offers" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Auto-accept offers above threshold</label>
                                                        </div>
                                                        <div id="auto_accept_fields" class="mt-3" style="display: none;">
                                                            <label for="auto_accept_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auto-accept Threshold ($)</label>
                                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                                                </div>
                                                                <input type="number" name="auto_accept_threshold" id="auto_accept_threshold" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg" placeholder="1500" min="1" step="0.01" value="{{ old('auto_accept_threshold') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    Description
                </h3>
                                        <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain Description</label>
                        <button type="button" id="ai-description-btn" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Write with AI
                        </button>
                    </div>
                    <div class="mt-1 relative">
                        <textarea name="description" id="description" rows="4" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('description') border-red-500 @enderror" placeholder="Describe your domain, its potential uses, and why it's valuable...">{{ old('description') }}</textarea>
                        <!-- AI Loading Overlay -->
                        <div id="ai-loading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-lg flex items-center justify-center hidden">
                            <div class="flex items-center space-x-2">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-purple-600"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">AI is writing your description...</span>
                            </div>
                        </div>
                                            </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tell potential buyers about the domain's potential and value. Use AI to generate a compelling description automatically.</p>
                                            @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                                <!-- Domain History -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Domain History
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="registration_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Date</label>
                            <div class="flex space-x-2">
                                <button type="button" id="quick-fill-btn" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Quick Fill
                                </button>
                                <button type="button" id="fetch-whois-btn" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Auto-fetch
                                </button>
                            </div>
                        </div>
                        <input type="date" name="registration_date" id="registration_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('registration_date') border-red-500 @enderror" value="{{ old('registration_date') }}">
                        @error('registration_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('expiry_date') border-red-500 @enderror" value="{{ old('expiry_date') }}">
                        @error('expiry_date')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Auto-fetch feature:</strong> Click "Auto-fetch" to automatically retrieve registration and expiry dates from WHOIS data. This helps ensure accurate domain information for your listing.
                            </p>
                            <div class="mt-2 text-xs text-blue-700 dark:text-blue-300">
                                <strong>Note:</strong> Some domains may have privacy protection enabled, which can prevent automatic date retrieval. In such cases, you can enter the dates manually or check your domain registrar's control panel.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                                    <!-- Domain Features -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    Domain Features
                </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div class="flex items-center">
                        <input type="checkbox" name="has_website" id="has_website" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('has_website') ? 'checked' : '' }}>
                        <label for="has_website" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                                    Has existing website
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                        <input type="checkbox" name="has_traffic" id="has_traffic" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('has_traffic') ? 'checked' : '' }}>
                        <label for="has_traffic" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                                    Has traffic/revenue
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                        <input type="checkbox" name="premium_domain" id="premium_domain" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('premium_domain') ? 'checked' : '' }}>
                        <label for="premium_domain" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                                    Premium domain
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Features -->
                                    <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <div class="p-2 bg-gradient-to-r from-teal-500 to-green-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    Additional Features
                </h3>
                                        <div>
                    <label for="additional_features" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Features</label>
                                            <div class="mt-1">
                        <textarea name="additional_features" id="additional_features" rows="3" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-lg @error('additional_features') border-red-500 @enderror" placeholder="Any additional features, SEO value, backlinks, etc...">{{ old('additional_features') }}</textarea>
                                            </div>
                                            @error('additional_features')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Verification Notice -->
                                    @if(!auth()->user()->isFullyVerified())
                                    <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Account Verification Required</h3>
                                                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                                    To list domains for sale, you need to complete your account verification first. You can save as draft until verification is complete.
                                                </p>
                                                <div class="mt-2">
                                                    <a href="{{ route('profile.verification') }}" class="text-sm font-medium text-yellow-800 dark:text-yellow-200 hover:text-yellow-900 dark:hover:text-yellow-100">
                                                        Complete Verification →
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-4">
                    @if(true) {{-- Temporarily allow all users --}}
                    <button type="submit" name="action" value="publish" id="publish-btn" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                {{ __('List Domain for Sale') }}
                                            </button>
                    @else
                    <button type="button" disabled class="inline-flex items-center px-6 py-3 bg-gray-400 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest shadow-lg cursor-not-allowed opacity-50">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                {{ __('Verification Required') }}
                                            </button>
                    @endif
                    <button type="submit" name="action" value="draft" id="draft-btn" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                                </svg>
                                                {{ __('Save as Draft') }}
                                            </button>
                                        </div>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                            Cancel
                                        </a>
                                    </div>
                                        </form>
    </div>
</div>

<!-- AI Description Generation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const aiButton = document.getElementById('ai-description-btn');
    const descriptionTextarea = document.getElementById('description');
    const loadingOverlay = document.getElementById('ai-loading');
    const whoisButton = document.getElementById('fetch-whois-btn');
    const quickFillButton = document.getElementById('quick-fill-btn');
    const registrationDateInput = document.getElementById('registration_date');
    const expiryDateInput = document.getElementById('expiry_date');
    
    // Sale options toggles
    const enableBuyNowCheckbox = document.getElementById('enable_buy_now');
    const buyNowFields = document.getElementById('buy_now_fields');
    const enableBiddingCheckbox = document.getElementById('enable_bidding');
    const biddingFields = document.getElementById('bidding_fields');
    const enableOffersCheckbox = document.getElementById('enable_offers');
    const offerFields = document.getElementById('offer_fields');
    const autoAcceptOffersCheckbox = document.getElementById('auto_accept_offers');
    const autoAcceptFields = document.getElementById('auto_accept_fields');
    
    aiButton.addEventListener('click', async function() {
        // Get form data
        const domainName = document.getElementById('domain_name').value;
        const domainExtension = document.getElementById('domain_extension').value;
        const category = document.getElementById('category').value;
        const askingPrice = document.getElementById('asking_price').value;
        const hasWebsite = document.getElementById('has_website').checked;
        const hasTraffic = document.getElementById('has_traffic').checked;
        const premiumDomain = document.getElementById('premium_domain').checked;
        
        // Validate required fields
        if (!domainName || !domainExtension) {
            alert('Please fill in the domain name and extension first.');
            return;
        }
        
        // Show loading state
        aiButton.disabled = true;
        aiButton.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1.5"></div>
            Generating...
        `;
        loadingOverlay.classList.remove('hidden');
        
        try {
            // Prepare data for AI generation
            const domainData = {
                domain_name: domainName,
                domain_extension: domainExtension,
                category: category || 'general',
                asking_price: askingPrice || 'Not specified',
                has_website: hasWebsite,
                has_traffic: hasTraffic,
                premium_domain: premiumDomain
            };
            
            // Generate AI description
            const description = generateAIDescription(domainData);
            
            // Fill the textarea with generated description
            descriptionTextarea.value = description;
            
            // Show success feedback
            aiButton.innerHTML = `
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Generated!
            `;
            aiButton.classList.remove('from-purple-600', 'to-pink-600', 'hover:from-purple-700', 'hover:to-pink-700');
            aiButton.classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                aiButton.innerHTML = `
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Write with AI
                `;
                aiButton.classList.remove('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                aiButton.classList.add('from-purple-600', 'to-pink-600', 'hover:from-purple-700', 'hover:to-pink-700');
                aiButton.disabled = false;
            }, 2000);
            
        } catch (error) {
            console.error('Error generating AI description:', error);
            alert('Sorry, there was an error generating the description. Please try again.');
        } finally {
            loadingOverlay.classList.add('hidden');
        }
    });
    
    function generateAIDescription(data) {
        const fullDomain = data.domain_name + data.domain_extension;
        const price = data.asking_price !== 'Not specified' ? `$${parseInt(data.asking_price).toLocaleString()}` : 'competitive pricing';
        
        // Generate description based on domain characteristics
        let description = `Discover the incredible potential of ${fullDomain} - a premium domain name that offers exceptional value and versatility.\n\n`;
        
        // Add category-specific content
        const categoryDescriptions = {
            'business': `Perfect for business ventures, corporate websites, and professional services. ${fullDomain} conveys trust, authority, and commercial viability.`,
            'technology': `Ideal for tech startups, software companies, and innovative digital solutions. ${fullDomain} represents cutting-edge technology and forward-thinking.`,
            'finance': `Excellent for financial services, fintech companies, and investment platforms. ${fullDomain} suggests stability, growth, and financial expertise.`,
            'health': `Perfect for healthcare providers, wellness brands, and medical services. ${fullDomain} conveys health, vitality, and professional care.`,
            'education': `Ideal for educational institutions, online learning platforms, and training services. ${fullDomain} represents knowledge, growth, and academic excellence.`,
            'entertainment': `Great for entertainment companies, media platforms, and creative services. ${fullDomain} suggests fun, engagement, and memorable experiences.`,
            'sports': `Perfect for sports teams, fitness brands, and athletic services. ${fullDomain} conveys energy, competition, and physical excellence.`,
            'travel': `Ideal for travel agencies, tourism companies, and hospitality services. ${fullDomain} represents adventure, exploration, and memorable journeys.`,
            'food': `Great for restaurants, food brands, and culinary services. ${fullDomain} suggests taste, quality, and delicious experiences.`,
            'fashion': `Perfect for fashion brands, clothing companies, and style services. ${fullDomain} conveys elegance, trendiness, and personal expression.`,
            'real-estate': `Ideal for real estate agencies, property companies, and housing services. ${fullDomain} represents stability, investment, and home ownership.`,
            'automotive': `Great for car dealerships, automotive services, and vehicle-related businesses. ${fullDomain} suggests reliability, performance, and mobility.`,
            'gaming': `Perfect for gaming companies, esports teams, and interactive entertainment. ${fullDomain} conveys excitement, competition, and digital fun.`,
            'crypto': `Ideal for cryptocurrency companies, blockchain services, and digital finance. ${fullDomain} represents innovation, security, and future technology.`
        };
        
        const categoryDesc = categoryDescriptions[data.category] || `Versatile and memorable, ${fullDomain} offers endless possibilities for branding and business development.`;
        description += categoryDesc + '\n\n';
        
        // Add features
        const features = [];
        if (data.has_website) features.push('existing website');
        if (data.has_traffic) features.push('established traffic');
        if (data.premium_domain) features.push('premium quality');
        
        if (features.length > 0) {
            description += `Key Features:\n`;
            features.forEach(feature => {
                description += `• ${feature.charAt(0).toUpperCase() + feature.slice(1)}\n`;
            });
            description += '\n';
        }
        
        // Add value proposition
        description += `Why This Domain?\n`;
        description += `• Memorable and brandable name\n`;
        description += `• Professional ${data.domain_extension} extension\n`;
        description += `• ${price} asking price\n`;
        description += `• Ready for immediate use\n`;
        description += `• High commercial potential\n\n`;
        
        // Add call to action
        description += `Don't miss this opportunity to own ${fullDomain}. This domain is perfect for entrepreneurs, businesses, and investors looking for a strong online presence. Contact us today to secure this valuable digital asset!`;
        
        return description;
    }
    
    // WHOIS API Integration
    whoisButton.addEventListener('click', async function() {
        const domainName = document.getElementById('domain_name').value;
        const domainExtension = document.getElementById('domain_extension').value;
        
        // Validate required fields
        if (!domainName || !domainExtension) {
            alert('Please fill in the domain name and extension first.');
            return;
        }
        
        const fullDomain = domainName + domainExtension;
        
        // Show loading state
        whoisButton.disabled = true;
        whoisButton.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1.5"></div>
            Fetching...
        `;
        
        try {
            // Use server-side API to avoid CORS issues
            const response = await fetch(`/api/whois/${encodeURIComponent(fullDomain)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success && result.data) {
                // Populate the date fields
                if (result.data.registration_date) {
                    registrationDateInput.value = result.data.registration_date;
                }
                if (result.data.expiry_date) {
                    expiryDateInput.value = result.data.expiry_date;
                }
                
                // Show success feedback
                whoisButton.innerHTML = `
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Fetched!
                `;
                whoisButton.classList.remove('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                whoisButton.classList.add('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                
                // Show success message
                showNotification('Domain dates fetched successfully!', 'success');
            } else {
                throw new Error(result.message || 'No data found');
            }
            
        } catch (error) {
            console.error('Error fetching WHOIS data:', error);
            
            // Show more specific error message
            let errorMessage = 'Unable to fetch domain dates. Please enter them manually.';
            
            if (error.message) {
                errorMessage = error.message;
            } else if (error.response && error.response.status === 404) {
                errorMessage = 'WHOIS data not available for this domain. This could be due to domain privacy protection. Please enter the dates manually.';
            } else if (error.response && error.response.status === 400) {
                errorMessage = 'Invalid domain format. Please check your domain name and extension.';
            }
            
            showNotification(errorMessage, 'error');
        } finally {
            // Reset button after 2 seconds
            setTimeout(() => {
                whoisButton.innerHTML = `
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Auto-fetch
                `;
                whoisButton.classList.remove('from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800');
                whoisButton.classList.add('from-blue-600', 'to-indigo-600', 'hover:from-blue-700', 'hover:to-indigo-700');
                whoisButton.disabled = false;
            }, 2000);
        }
    });
    
    // Quick Fill functionality
    quickFillButton.addEventListener('click', function() {
        const domainName = document.getElementById('domain_name').value;
        const domainExtension = document.getElementById('domain_extension').value;
        
        // Validate required fields
        if (!domainName || !domainExtension) {
            alert('Please fill in the domain name and extension first.');
            return;
        }
        
        // Show loading state
        quickFillButton.disabled = true;
        quickFillButton.innerHTML = `
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-1.5"></div>
            Filling...
        `;
        
        try {
            // Generate estimated dates based on common patterns
            const estimatedDates = generateEstimatedDates(domainName, domainExtension);
            
            // Fill the date fields
            if (estimatedDates.registrationDate) {
                registrationDateInput.value = estimatedDates.registrationDate;
            }
            if (estimatedDates.expiryDate) {
                expiryDateInput.value = estimatedDates.expiryDate;
            }
            
            // Show success feedback
            quickFillButton.innerHTML = `
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Filled!
            `;
            quickFillButton.classList.remove('from-green-600', 'to-emerald-600', 'hover:from-green-700', 'hover:to-emerald-700');
            quickFillButton.classList.add('from-blue-600', 'to-blue-700', 'hover:from-blue-700', 'hover:to-blue-800');
            
            // Show success message
            showNotification('Estimated dates filled! Please verify and adjust as needed.', 'success');
            
        } catch (error) {
            console.error('Error in quick fill:', error);
            showNotification('Unable to generate estimated dates. Please enter them manually.', 'error');
        } finally {
            // Reset button after 2 seconds
            setTimeout(() => {
                quickFillButton.innerHTML = `
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Fill
                `;
                quickFillButton.classList.remove('from-blue-600', 'to-blue-700', 'hover:from-blue-700', 'hover:to-blue-800');
                quickFillButton.classList.add('from-green-600', 'to-emerald-600', 'hover:from-green-700', 'hover:to-emerald-700');
                quickFillButton.disabled = false;
            }, 2000);
        }
    });
    
    function generateEstimatedDates(domainName, domainExtension) {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        
        // Common domain registration patterns
        const registrationYear = currentYear - Math.floor(Math.random() * 5) - 1; // 1-5 years ago
        const registrationMonth = Math.floor(Math.random() * 12) + 1;
        const registrationDay = Math.floor(Math.random() * 28) + 1;
        
        // Registration date (1-5 years ago)
        const registrationDate = new Date(registrationYear, registrationMonth - 1, registrationDay);
        
        // Expiry date (typically 1-10 years from registration)
        const yearsToAdd = Math.floor(Math.random() * 9) + 1; // 1-9 years
        const expiryDate = new Date(registrationDate);
        expiryDate.setFullYear(expiryDate.getFullYear() + yearsToAdd);
        
        // Format dates for HTML input
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        return {
            registrationDate: formatDate(registrationDate),
            expiryDate: formatDate(expiryDate)
        };
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${
                        type === 'success' ? 'M5 13l4 4L19 7' :
                        type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                        'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }
    
    // Button click debugging
    const publishBtn = document.getElementById('publish-btn');
    const draftBtn = document.getElementById('draft-btn');
    
    if (publishBtn) {
        publishBtn.addEventListener('click', function(e) {
            console.log('Publish button clicked');
            console.log('Button value:', this.value);
        });
    }
    
    if (draftBtn) {
        draftBtn.addEventListener('click', function(e) {
            console.log('Draft button clicked');
            console.log('Button value:', this.value);
        });
    }

    // Form validation and submission
    const form = document.getElementById('domain-form');
    if (form) {
        // Clear any existing error messages
        function clearErrorMessages() {
            const errorElements = document.querySelectorAll('.field-error');
            errorElements.forEach(element => element.remove());
        }
        
        // Show error message for a field
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            
            // Remove existing error for this field
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error mt-1 text-sm text-red-600 dark:text-red-400';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
            
            // Add error styling to field
            field.classList.add('border-red-500');
            field.classList.remove('border-gray-300', 'dark:border-gray-600');
        }
        
        // Show general error message
        function showGeneralError(message) {
            // Remove existing general error
            const existingError = document.getElementById('general-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Add general error message
            const errorDiv = document.createElement('div');
            errorDiv.id = 'general-error';
            errorDiv.className = 'mb-4 p-4 bg-red-50 border border-red-200 rounded-lg';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800 font-medium">${message}</span>
                </div>
            `;
            
            // Insert before the form
            form.parentNode.insertBefore(errorDiv, form);
        }
        
        // Validate form fields
        function validateForm() {
            clearErrorMessages();
            let isValid = true;
            let errorMessages = [];
            
            // Required field validation
            const domainName = document.getElementById('domain_name').value.trim();
            const domainExtension = document.getElementById('domain_extension').value;
            const askingPrice = document.getElementById('asking_price').value;
            
            if (!domainName) {
                showFieldError('domain_name', 'Domain name is required.');
                isValid = false;
            } else if (!/^[a-zA-Z0-9-]+$/.test(domainName)) {
                showFieldError('domain_name', 'Domain name can only contain letters, numbers, and hyphens.');
                isValid = false;
            }
            
            if (!domainExtension) {
                showFieldError('domain_extension', 'Domain extension is required.');
                isValid = false;
            }
            
            if (!askingPrice) {
                showFieldError('asking_price', 'Asking price is required.');
                isValid = false;
            } else if (parseFloat(askingPrice) < 0.01) {
                showFieldError('asking_price', 'Asking price must be at least $0.01.');
                isValid = false;
            } else if (parseFloat(askingPrice) > 9999999.99) {
                showFieldError('asking_price', 'Asking price cannot exceed $9,999,999.99.');
                isValid = false;
            }
            
            // Check if user is verified (only for publish action)
            const submitButton = document.querySelector('button[type="submit"]:focus');
            if (submitButton && submitButton.value === 'publish') {
                const isVerified = {{ auth()->user()->isFullyVerified() ? 'true' : 'false' }};
                if (!isVerified) {
                    showGeneralError('You must complete your account verification before listing domains for sale. Please complete PayPal and Government ID verification first.');
                    isValid = false;
                }
            }
            
            // Validate pricing options if enabled
            const enableBuyNow = document.getElementById('enable_buy_now')?.checked;
            const enableOffers = document.getElementById('enable_offers')?.checked;
            const enableBidding = document.getElementById('enable_bidding')?.checked;
            
            if (enableBuyNow) {
                const buyNowPrice = document.getElementById('buy_now_price')?.value;
                if (buyNowPrice && parseFloat(buyNowPrice) < 0.01) {
                    showFieldError('buy_now_price', 'Buy Now price must be at least $0.01.');
                    isValid = false;
                }
            }
            
            if (enableOffers) {
                const minOffer = document.getElementById('minimum_offer')?.value;
                const maxOffer = document.getElementById('maximum_offer')?.value;
                const autoAccept = document.getElementById('auto_accept_threshold')?.value;
                
                if (minOffer && parseFloat(minOffer) < 0.01) {
                    showFieldError('minimum_offer', 'Minimum offer must be at least $0.01.');
                    isValid = false;
                }
                
                if (maxOffer && parseFloat(maxOffer) < 0.01) {
                    showFieldError('maximum_offer', 'Maximum offer must be at least $0.01.');
                    isValid = false;
                }
                
                if (minOffer && maxOffer && parseFloat(maxOffer) <= parseFloat(minOffer)) {
                    showFieldError('maximum_offer', 'Maximum offer should be higher than minimum offer.');
                    isValid = false;
                }
                
                if (document.getElementById('auto_accept_offers')?.checked) {
                    if (autoAccept && parseFloat(autoAccept) < 0.01) {
                        showFieldError('auto_accept_threshold', 'Auto-accept threshold must be at least $0.01.');
                        isValid = false;
                    }
                    
                    if (minOffer && autoAccept && parseFloat(autoAccept) <= parseFloat(minOffer)) {
                        showFieldError('auto_accept_threshold', 'Auto-accept threshold should be higher than minimum offer.');
                        isValid = false;
                    }
                }
            }
            
            if (enableBidding) {
                const startingBid = document.getElementById('starting_bid')?.value;
                const reservePrice = document.getElementById('reserve_price')?.value;
                
                if (startingBid && parseFloat(startingBid) < 0.01) {
                    showFieldError('starting_bid', 'Starting bid must be at least $0.01.');
                    isValid = false;
                }
                
                if (reservePrice && parseFloat(reservePrice) < 0.01) {
                    showFieldError('reserve_price', 'Reserve price must be at least $0.01.');
                    isValid = false;
                }
                
                if (startingBid && reservePrice && parseFloat(reservePrice) < parseFloat(startingBid)) {
                    showFieldError('reserve_price', 'Reserve price should be at least the starting bid amount.');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        form.addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            if (!validateForm()) {
                e.preventDefault();
                console.log('Form validation failed');
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            
            // Show loading state
            const submitButtons = document.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                button.innerHTML = `
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                    Processing...
                `;
            });
        });
        
        // Real-time validation as user types
        const domainNameField = document.getElementById('domain_name');
        const domainExtensionField = document.getElementById('domain_extension');
        const askingPriceField = document.getElementById('asking_price');
        
        if (domainNameField) {
            domainNameField.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && !/^[a-zA-Z0-9-]+$/.test(value)) {
                    showFieldError('domain_name', 'Domain name can only contain letters, numbers, and hyphens.');
                } else {
                    clearFieldError('domain_name');
                }
            });
        }
        
        if (askingPriceField) {
            askingPriceField.addEventListener('blur', function() {
                const value = parseFloat(this.value);
                if (this.value && (isNaN(value) || value < 0.01)) {
                    showFieldError('asking_price', 'Asking price must be at least $0.01.');
                } else if (this.value && value > 9999999.99) {
                    showFieldError('asking_price', 'Asking price cannot exceed $9,999,999.99.');
                } else {
                    clearFieldError('asking_price');
                }
            });
        }
        
        // Clear field error helper
        function clearFieldError(fieldId) {
            const field = document.getElementById(fieldId);
            if (!field) return;
            
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            
            field.classList.remove('border-red-500');
            field.classList.add('border-gray-300', 'dark:border-gray-600');
        }
    }

    // Sale options toggle functionality
    if (enableBuyNowCheckbox) {
        enableBuyNowCheckbox.addEventListener('change', function() {
            if (this.checked) {
                buyNowFields.style.display = 'block';
            } else {
                buyNowFields.style.display = 'none';
            }
        });
        
        // Initialize on page load
        if (enableBuyNowCheckbox.checked) {
            buyNowFields.style.display = 'block';
        }
    }
    
    if (enableBiddingCheckbox) {
        enableBiddingCheckbox.addEventListener('change', function() {
            if (this.checked) {
                biddingFields.style.display = 'block';
            } else {
                biddingFields.style.display = 'none';
            }
        });
        
        // Initialize on page load
        if (enableBiddingCheckbox.checked) {
            biddingFields.style.display = 'block';
        }
    }
    
    if (enableOffersCheckbox) {
        enableOffersCheckbox.addEventListener('change', function() {
            if (this.checked) {
                offerFields.style.display = 'block';
            } else {
                offerFields.style.display = 'none';
            }
        });
        
        // Initialize on page load
        if (enableOffersCheckbox.checked) {
            offerFields.style.display = 'block';
        }
    }
    
    if (autoAcceptOffersCheckbox) {
        autoAcceptOffersCheckbox.addEventListener('change', function() {
            if (this.checked) {
                autoAcceptFields.style.display = 'block';
            } else {
                autoAcceptFields.style.display = 'none';
            }
        });
        
        // Initialize on page load
        if (autoAcceptOffersCheckbox.checked) {
            autoAcceptFields.style.display = 'block';
        }
    }

    // Auto-update auction end date when start date changes
    function updateAuctionEndDate() {
        const startDateInput = document.getElementById('auction_start');
        const endDateInput = document.getElementById('auction_end');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    const startDate = new Date(this.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(endDate.getDate() + 90); // Add 90 days
                    
                    // Format for datetime-local input
                    const year = endDate.getFullYear();
                    const month = String(endDate.getMonth() + 1).padStart(2, '0');
                    const day = String(endDate.getDate()).padStart(2, '0');
                    const hours = String(endDate.getHours()).padStart(2, '0');
                    const minutes = String(endDate.getMinutes()).padStart(2, '0');
                    
                    endDateInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
                }
            });
        }
    }

    // Initialize auction date functionality
    updateAuctionEndDate();
});
</script>
@endsection
