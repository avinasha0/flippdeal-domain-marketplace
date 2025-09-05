@extends('layouts.app')

@section('title', 'Edit Domain - ' . $domain->full_domain)

@section('content')
@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Domain</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Update your domain listing information for <strong>{{ $domain->full_domain }}</strong>.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Domain
            </a>
            <a href="{{ route('my.domains.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                All Domains
            </a>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
    <div class="px-6 py-8">
        <form method="post" action="{{ route('domains.update', $domain) }}" class="space-y-8">
            @csrf
            @method('PATCH')

            <!-- Domain Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                    Domain Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="domain_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain Name</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" name="domain_name" id="domain_name" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('domain_name') border-red-500 @enderror" placeholder="example" value="{{ old('domain_name', $domain->domain_name) }}" required>
                        </div>
                        @error('domain_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="domain_extension" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Extension</label>
                        <select name="domain_extension" id="domain_extension" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('domain_extension') border-red-500 @enderror">
                            <option value="">Select Extension</option>
                            <option value=".com" {{ old('domain_extension', $domain->domain_extension) == '.com' ? 'selected' : '' }}>.com</option>
                            <option value=".net" {{ old('domain_extension', $domain->domain_extension) == '.net' ? 'selected' : '' }}>.net</option>
                            <option value=".org" {{ old('domain_extension', $domain->domain_extension) == '.org' ? 'selected' : '' }}>.org</option>
                            <option value=".io" {{ old('domain_extension', $domain->domain_extension) == '.io' ? 'selected' : '' }}>.io</option>
                            <option value=".co" {{ old('domain_extension', $domain->domain_extension) == '.co' ? 'selected' : '' }}>.co</option>
                            <option value=".ai" {{ old('domain_extension', $domain->domain_extension) == '.ai' ? 'selected' : '' }}>.ai</option>
                            <option value=".app" {{ old('domain_extension', $domain->domain_extension) == '.app' ? 'selected' : '' }}>.app</option>
                            <option value=".dev" {{ old('domain_extension', $domain->domain_extension) == '.dev' ? 'selected' : '' }}>.dev</option>
                            <option value=".tech" {{ old('domain_extension', $domain->domain_extension) == '.tech' ? 'selected' : '' }}>.tech</option>
                            <option value=".store" {{ old('domain_extension', $domain->domain_extension) == '.store' ? 'selected' : '' }}>.store</option>
                            <option value=".shop" {{ old('domain_extension', $domain->domain_extension) == '.shop' ? 'selected' : '' }}>.shop</option>
                            <option value=".blog" {{ old('domain_extension', $domain->domain_extension) == '.blog' ? 'selected' : '' }}>.blog</option>
                            <option value=".news" {{ old('domain_extension', $domain->domain_extension) == '.news' ? 'selected' : '' }}>.news</option>
                            <option value=".info" {{ old('domain_extension', $domain->domain_extension) == '.info' ? 'selected' : '' }}>.info</option>
                            <option value=".biz" {{ old('domain_extension', $domain->domain_extension) == '.biz' ? 'selected' : '' }}>.biz</option>
                            <option value=".me" {{ old('domain_extension', $domain->domain_extension) == '.me' ? 'selected' : '' }}>.me</option>
                            <option value=".tv" {{ old('domain_extension', $domain->domain_extension) == '.tv' ? 'selected' : '' }}>.tv</option>
                            <option value=".cc" {{ old('domain_extension', $domain->domain_extension) == '.cc' ? 'selected' : '' }}>.cc</option>
                            <option value=".ws" {{ old('domain_extension', $domain->domain_extension) == '.ws' ? 'selected' : '' }}>.ws</option>
                            <option value=".xyz" {{ old('domain_extension', $domain->domain_extension) == '.xyz' ? 'selected' : '' }}>.xyz</option>
                        </select>
                        @error('domain_extension')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Category -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Pricing & Category
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="asking_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asking Price ($)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="asking_price" id="asking_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('asking_price') border-red-500 @enderror" placeholder="1000" min="1" step="0.01" value="{{ old('asking_price', $domain->asking_price) }}" required>
                        </div>
                        @error('asking_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                        <select name="category" id="category" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('category') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            <option value="business" {{ old('category', $domain->category) == 'business' ? 'selected' : '' }}>Business</option>
                            <option value="technology" {{ old('category', $domain->category) == 'technology' ? 'selected' : '' }}>Technology</option>
                            <option value="finance" {{ old('category', $domain->category) == 'finance' ? 'selected' : '' }}>Finance</option>
                            <option value="health" {{ old('category', $domain->category) == 'health' ? 'selected' : '' }}>Health & Fitness</option>
                            <option value="education" {{ old('category', $domain->category) == 'education' ? 'selected' : '' }}>Education</option>
                            <option value="entertainment" {{ old('category', $domain->category) == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                            <option value="sports" {{ old('category', $domain->category) == 'sports' ? 'selected' : '' }}>Sports</option>
                            <option value="travel" {{ old('category', $domain->category) == 'travel' ? 'selected' : '' }}>Travel</option>
                            <option value="food" {{ old('category', $domain->category) == 'food' ? 'selected' : '' }}>Food & Dining</option>
                            <option value="fashion" {{ old('category', $domain->category) == 'fashion' ? 'selected' : '' }}>Fashion & Beauty</option>
                            <option value="real-estate" {{ old('category', $domain->category) == 'real-estate' ? 'selected' : '' }}>Real Estate</option>
                            <option value="automotive" {{ old('category', $domain->category) == 'automotive' ? 'selected' : '' }}>Automotive</option>
                            <option value="gaming" {{ old('category', $domain->category) == 'gaming' ? 'selected' : '' }}>Gaming</option>
                            <option value="crypto" {{ old('category', $domain->category) == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                            <option value="other" {{ old('category', $domain->category) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Description
                </h3>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Domain Description</label>
                    <div class="mt-1">
                        <textarea name="description" id="description" rows="4" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('description') border-red-500 @enderror" placeholder="Describe your domain, its potential uses, and why it's valuable...">{{ old('description', $domain->description) }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tell potential buyers about the domain's potential and value.</p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Domain History -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Domain History
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="registration_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Date</label>
                        <input type="date" name="registration_date" id="registration_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('registration_date') border-red-500 @enderror" value="{{ old('registration_date', $domain->registration_date ? $domain->registration_date->format('Y-m-d') : '') }}">
                        @error('registration_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('expiry_date') border-red-500 @enderror" value="{{ old('expiry_date', $domain->expiry_date ? $domain->expiry_date->format('Y-m-d') : '') }}">
                        @error('expiry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Domain Features -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Domain Features
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="has_website" id="has_website" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('has_website', $domain->has_website) ? 'checked' : '' }}>
                        <label for="has_website" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Has existing website
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="has_traffic" id="has_traffic" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('has_traffic', $domain->has_traffic) ? 'checked' : '' }}>
                        <label for="has_traffic" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Has traffic/revenue
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="premium_domain" id="premium_domain" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('premium_domain', $domain->premium_domain) ? 'checked' : '' }}>
                        <label for="premium_domain" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Premium domain
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bidding Options -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Bidding Options
                </h3>
                <div class="space-y-6">
                    <!-- Buy It Now Option -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_bin" id="enable_bin" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('enable_bin', $domain->bin_price ? true : false) ? 'checked' : '' }} onchange="toggleBinFields()">
                                <label for="enable_bin" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Enable Buy It Now (BIN)
                                </label>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                Instant Purchase
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Allow buyers to purchase this domain immediately at a fixed price without waiting for offers.</p>
                        
                        <div id="bin_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->bin_price ? '' : 'hidden' }}">
                            <div>
                                <label for="bin_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buy It Now Price ($)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="bin_price" id="bin_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('bin_price') border-red-500 @enderror" placeholder="1500" min="1" step="0.01" value="{{ old('bin_price', $domain->bin_price) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set a price for immediate purchase. Usually higher than asking price.</p>
                                @error('bin_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Auction/Bidding Option -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_bidding" id="enable_bidding" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('enable_bidding', $domain->enable_bidding) ? 'checked' : '' }} onchange="toggleBiddingFields()">
                                <label for="enable_bidding" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Enable Auction/Bidding
                                </label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                    Competitive Bidding
                                </span>
                                @if($domain->enable_bidding)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($domain->auction_status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                        @elseif($domain->auction_status === 'scheduled') bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200
                                        @elseif($domain->auction_status === 'ended') bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200
                                        @else bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                        @endif">
                                        {{ ucfirst($domain->auction_status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Allow buyers to bid competitively on your domain with a starting price and auction end time.</p>
                        @if($domain->enable_bidding && !$domain->isReadyForBidding())
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span class="text-sm text-yellow-800 dark:text-yellow-200">
                                        <strong>Setup Required:</strong> To enable bidding, you must set a starting bid amount and auction start/end times.
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <div id="bidding_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->enable_bidding ? '' : 'hidden' }}">
                            <div>
                                <label for="starting_bid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Starting Bid ($)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="starting_bid" id="starting_bid" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('starting_bid') border-red-500 @enderror" placeholder="500" min="1" step="0.01" value="{{ old('starting_bid', $domain->starting_bid) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum starting bid for the auction.</p>
                                @error('starting_bid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reserve_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reserve Price ($)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="reserve_price" id="reserve_price" class="block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('reserve_price') border-red-500 @enderror" placeholder="800" min="1" step="0.01" value="{{ old('reserve_price', $domain->reserve_price) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum price to sell (optional).</p>
                                @error('reserve_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="auction_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auction Start</label>
                                <input type="datetime-local" name="auction_start" id="auction_start" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('auction_start') border-red-500 @enderror" value="{{ old('auction_start', $domain->auction_start ? $domain->auction_start->format('Y-m-d\TH:i') : '') }}">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">When the auction should start.</p>
                                @error('auction_start')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="auction_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Auction End</label>
                                <input type="datetime-local" name="auction_end" id="auction_end" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('auction_end') border-red-500 @enderror" value="{{ old('auction_end', $domain->auction_end ? $domain->auction_end->format('Y-m-d\TH:i') : '') }}">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">When the auction should end.</p>
                                @error('auction_end')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="minimum_bid_increment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Bid Increment ($)</label>
                                <input type="number" name="minimum_bid_increment" id="minimum_bid_increment" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('minimum_bid_increment') border-red-500 @enderror" placeholder="10" min="1" value="{{ old('minimum_bid_increment', $domain->minimum_bid_increment) }}">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum amount each bid must increase by.</p>
                                @error('minimum_bid_increment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="auto_extend" id="auto_extend" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('auto_extend', $domain->auto_extend) ? 'checked' : '' }}>
                                        <label for="auto_extend" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Auto-extend auction if bids near end
                                        </label>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Automatically extend auction time if bids are placed near the end.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Offer Options -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="accepts_offers" id="accepts_offers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded" {{ old('accepts_offers', $domain->accepts_offers) ? 'checked' : '' }} onchange="toggleOfferFields()">
                                <label for="accepts_offers" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Accept Offers
                                </label>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Negotiable
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Allow potential buyers to submit offers below your asking price.</p>
                        
                        <div id="offer_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->accepts_offers ? '' : 'hidden' }}">
                            <div>
                                <label for="minimum_offer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Offer ($)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="minimum_offer" id="minimum_offer" class="mt-1 block w-full pl-7 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-purple-500 focus:border-purple-500 rounded-md @error('minimum_offer') border-red-500 @enderror" placeholder="800" min="1" step="0.01" value="{{ old('minimum_offer', $domain->minimum_offer) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Set the lowest offer you're willing to consider.</p>
                                @error('minimum_offer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Features -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Additional Features
                </h3>
                <div>
                    <label for="additional_features" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Features</label>
                    <div class="mt-1">
                        <textarea name="additional_features" id="additional_features" rows="3" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 rounded-md @error('additional_features') border-red-500 @enderror" placeholder="Any additional features, SEO value, backlinks, etc...">{{ old('additional_features', $domain->additional_features) }}</textarea>
                    </div>
                    @error('additional_features')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Update Domain
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Change to Draft Form - Outside main form to avoid nesting -->
@if(in_array($domain->status, ['active', 'inactive']) && !$domain->hasPendingActions())
<div class="mt-6 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-800">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-100">Change Domain Status</h3>
                <p class="text-sm text-orange-700 dark:text-orange-300">
                    Change this domain to draft status to make it invisible to buyers and allow editing.
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <form method="POST" action="{{ route('domains.change-to-draft', $domain) }}" class="inline" onsubmit="return handleDraftChange(event)">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Change to Draft
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function toggleBinFields() {
        const enableBin = document.getElementById('enable_bin');
        const binFields = document.getElementById('bin_fields');
        const binPriceInput = document.getElementById('bin_price');
        
        if (enableBin.checked) {
            binFields.classList.remove('hidden');
            binPriceInput.required = true;
        } else {
            binFields.classList.add('hidden');
            binPriceInput.required = false;
            binPriceInput.value = '';
        }
    }

    function toggleOfferFields() {
        const acceptsOffers = document.getElementById('accepts_offers');
        const offerFields = document.getElementById('offer_fields');
        const minimumOfferInput = document.getElementById('minimum_offer');
        
        if (acceptsOffers.checked) {
            offerFields.classList.remove('hidden');
            minimumOfferInput.required = false; // Make it optional, not required
        } else {
            offerFields.classList.add('hidden');
            minimumOfferInput.required = false;
            // Don't clear the value - preserve it in case user rechecks the box
        }
    }

    function toggleBiddingFields() {
        const enableBidding = document.getElementById('enable_bidding');
        const biddingFields = document.getElementById('bidding_fields');
        
        if (enableBidding.checked) {
            biddingFields.classList.remove('hidden');
        } else {
            biddingFields.classList.add('hidden');
            // Don't clear values - preserve them in case user rechecks the box
        }
    }

    // Handle Change to Draft form submission
    function handleDraftChange(event) {
        console.log('Change to Draft button clicked');
        console.log('Event:', event);
        console.log('Form:', event.target.form);
        
        const confirmed = confirm('Are you sure you want to change this domain to draft status?\n\nThis will:\n• Make the domain invisible to buyers\n• Allow you to edit and verify before republishing\n• Preserve all current settings\n\nThis action can be undone by publishing the domain again.');
        
        if (confirmed) {
            console.log('User confirmed - submitting form');
            console.log('Form action:', event.target.form.action);
            console.log('Form method:', event.target.form.method);
            
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Changing to Draft...';
            button.disabled = true;
            
            // Let the form submit naturally
            console.log('Returning true to allow form submission');
            return true;
        } else {
            console.log('User cancelled');
            return false;
        }
    }

    // Initialize fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleBinFields();
        toggleOfferFields();
        toggleBiddingFields();
    });
</script>
@endsection