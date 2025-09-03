<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlippDeal') }} - Edit {{ $domain->full_domain }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white">
            <!-- Custom Header -->
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo and App Name -->
                        <div class="flex items-center space-x-8">
                            <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">F</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h1 class="text-xl font-bold text-gray-900">FlippDeal</h1>
                                </div>
                            </a>

                            <!-- Explore Dropdown -->
                            <div class="relative" x-data="{ exploreOpen: false }">
                                <button @click="exploreOpen = !exploreOpen" class="flex items-center text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    Explore
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Explore Dropdown Menu -->
                                <div x-show="exploreOpen" @click.away="exploreOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                            </svg>
                                            Domains
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Websites
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="flex items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="ml-2 text-gray-700 font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 h-4 w-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Profile Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile Settings
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <!-- Page Header -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900">Edit Domain</h1>
                                    <p class="mt-2 text-gray-600">Update your domain listing information.</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Back to Domain
                                    </a>
                                    <a href="{{ route('my.domains.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        All Domains
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <form method="post" action="{{ route('domains.update', $domain) }}" class="space-y-8">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Domain Information -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Domain Information</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="domain_name" class="block text-sm font-medium text-gray-700">Domain Name</label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <input type="text" name="domain_name" id="domain_name" class="block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('domain_name') border-red-500 @enderror" placeholder="example" value="{{ old('domain_name', $domain->domain_name) }}" required>
                                                </div>
                                                @error('domain_name')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="domain_extension" class="block text-sm font-medium text-gray-700">Extension</label>
                                                <select name="domain_extension" id="domain_extension" class="mt-1 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('domain_extension') border-red-500 @enderror">
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
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Category</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="asking_price" class="block text-sm font-medium text-gray-700">Asking Price ($)</label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">$</span>
                                                    </div>
                                                    <input type="number" name="asking_price" id="asking_price" class="block w-full pl-7 border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('asking_price') border-red-500 @enderror" placeholder="1000" min="1" step="0.01" value="{{ old('asking_price', $domain->asking_price) }}" required>
                                                </div>
                                                @error('asking_price')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                                <select name="category" id="category" class="mt-1 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('category') border-red-500 @enderror">
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
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700">Domain Description</label>
                                            <div class="mt-1">
                                                <textarea name="description" id="description" rows="4" class="block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('description') border-red-500 @enderror" placeholder="Describe your domain, its potential uses, and why it's valuable...">{{ old('description', $domain->description) }}</textarea>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Tell potential buyers about the domain's potential and value.</p>
                                            @error('description')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Domain History -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Domain History</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="registration_date" class="block text-sm font-medium text-gray-700">Registration Date</label>
                                                <input type="date" name="registration_date" id="registration_date" class="mt-1 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('registration_date') border-red-500 @enderror" value="{{ old('registration_date', $domain->registration_date ? $domain->registration_date->format('Y-m-d') : '') }}">
                                                @error('registration_date')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                                <input type="date" name="expiry_date" id="expiry_date" class="mt-1 block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('expiry_date') border-red-500 @enderror" value="{{ old('expiry_date', $domain->expiry_date ? $domain->expiry_date->format('Y-m-d') : '') }}">
                                                @error('expiry_date')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Domain Features -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Domain Features</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="has_website" id="has_website" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('has_website', $domain->has_website) ? 'checked' : '' }}>
                                                <label for="has_website" class="ml-2 block text-sm text-gray-900">
                                                    Has existing website
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" name="has_traffic" id="has_traffic" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('has_traffic', $domain->has_traffic) ? 'checked' : '' }}>
                                                <label for="has_traffic" class="ml-2 block text-sm text-gray-900">
                                                    Has traffic/revenue
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" name="premium_domain" id="premium_domain" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('premium_domain', $domain->premium_domain) ? 'checked' : '' }}>
                                                <label for="premium_domain" class="ml-2 block text-sm text-gray-900">
                                                    Premium domain
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bidding Options -->
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Bidding Options</h3>
                                        <div class="space-y-6">
                                            <!-- Buy It Now Option -->
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="enable_bin" id="enable_bin" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('enable_bin', $domain->bin_price ? true : false) ? 'checked' : '' }} onchange="toggleBinFields()">
                                                        <label for="enable_bin" class="ml-2 block text-sm font-medium text-gray-900">
                                                            Enable Buy It Now (BIN)
                                                        </label>
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Instant Purchase
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-4">Allow buyers to purchase this domain immediately at a fixed price without waiting for offers.</p>
                                                
                                                <div id="bin_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->bin_price ? '' : 'hidden' }}">
                                                    <div>
                                                        <label for="bin_price" class="block text-sm font-medium text-gray-700">Buy It Now Price ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="bin_price" id="bin_price" class="block w-full pl-7 border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('bin_price') border-red-500 @enderror" placeholder="1500" min="1" step="0.01" value="{{ old('bin_price', $domain->bin_price) }}">
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500">Set a price for immediate purchase. Usually higher than asking price.</p>
                                                        @error('bin_price')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Auction/Bidding Option -->
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="enable_bidding" id="enable_bidding" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" {{ old('enable_bidding', $domain->enable_bidding) ? 'checked' : '' }} onchange="toggleBiddingFields()">
                                                        <label for="enable_bidding" class="ml-2 block text-sm font-medium text-gray-900">
                                                            Enable Auction/Bidding
                                                        </label>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            Competitive Bidding
                                                        </span>
                                                        @if($domain->enable_bidding)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                @if($domain->auction_status === 'active') bg-green-100 text-green-800
                                                                @elseif($domain->auction_status === 'scheduled') bg-blue-100 text-blue-800
                                                                @elseif($domain->auction_status === 'ended') bg-gray-100 text-gray-800
                                                                @else bg-yellow-100 text-yellow-800
                                                                @endif">
                                                                {{ ucfirst($domain->auction_status) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-4">Allow buyers to bid competitively on your domain with a starting price and auction end time.</p>
                                                @if($domain->enable_bidding && !$domain->isReadyForBidding())
                                                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-4">
                                                        <div class="flex items-center">
                                                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                            </svg>
                                                            <span class="text-sm text-yellow-800">
                                                                <strong>Setup Required:</strong> To enable bidding, you must set a starting bid amount and auction start/end times.
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <div id="bidding_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->enable_bidding ? '' : 'hidden' }}">
                                                    <div>
                                                        <label for="starting_bid" class="block text-sm font-medium text-gray-700">Starting Bid ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="starting_bid" id="starting_bid" class="block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('starting_bid') border-red-500 @enderror" placeholder="500" min="1" step="0.01" value="{{ old('starting_bid', $domain->starting_bid) }}">
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500">Minimum starting bid for the auction.</p>
                                                        @error('starting_bid')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="reserve_price" class="block text-sm font-medium text-gray-700">Reserve Price ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="reserve_price" id="reserve_price" class="block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('reserve_price') border-red-500 @enderror" placeholder="800" min="1" step="0.01" value="{{ old('reserve_price', $domain->reserve_price) }}">
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500">Minimum price to sell (optional).</p>
                                                        @error('reserve_price')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="auction_start" class="block text-sm font-medium text-gray-700">Auction Start</label>
                                                        <input type="datetime-local" name="auction_start" id="auction_start" class="mt-1 block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('auction_start') border-red-500 @enderror" value="{{ old('auction_start', $domain->auction_start ? $domain->auction_start->format('Y-m-d\TH:i') : '') }}">
                                                        <p class="mt-1 text-sm text-gray-500">When the auction should start.</p>
                                                        @error('auction_start')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="auction_end" class="block text-sm font-medium text-gray-700">Auction End</label>
                                                        <input type="datetime-local" name="auction_end" id="auction_end" class="mt-1 block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('auction_end') border-red-500 @enderror" value="{{ old('auction_end', $domain->auction_end ? $domain->auction_end->format('Y-m-d\TH:i') : '') }}">
                                                        <p class="mt-1 text-sm text-gray-500">When the auction should end.</p>
                                                        @error('auction_end')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div>
                                                        <label for="minimum_bid_increment" class="block text-sm font-medium text-gray-700">Min Bid Increment ($)</label>
                                                        <input type="number" name="minimum_bid_increment" id="minimum_bid_increment" class="mt-1 block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('minimum_bid_increment') border-red-500 @enderror" placeholder="10" min="1" value="{{ old('minimum_bid_increment', $domain->minimum_bid_increment) }}">
                                                        <p class="mt-1 text-sm text-gray-500">Minimum amount each bid must increase by.</p>
                                                        @error('minimum_bid_increment')
                                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <div class="flex items-center space-x-4">
                                                            <div class="flex items-center">
                                                                <input type="checkbox" name="auto_extend" id="auto_extend" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" {{ old('auto_extend', $domain->auto_extend) ? 'checked' : '' }}>
                                                                <label for="auto_extend" class="ml-2 block text-sm font-medium text-gray-700">
                                                                    Auto-extend auction if bids near end
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500">Automatically extend auction time if bids are placed near the end.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Offer Options -->
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="accepts_offers" id="accepts_offers" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('accepts_offers', $domain->accepts_offers) ? 'checked' : '' }} onchange="toggleOfferFields()">
                                                        <label for="accepts_offers" class="ml-2 block text-sm font-medium text-gray-900">
                                                            Accept Offers
                                                        </label>
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Negotiable
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-4">Allow potential buyers to submit offers below your asking price.</p>
                                                
                                                <div id="offer_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $domain->accepts_offers ? '' : 'hidden' }}">
                                                    <div>
                                                        <label for="minimum_offer" class="block text-sm font-medium text-gray-700">Minimum Offer ($)</label>
                                                        <div class="mt-1 relative rounded-md shadow-sm">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" name="minimum_offer" id="minimum_offer" class="mt-1 block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('minimum_offer') border-red-500 @enderror" placeholder="800" min="1" step="0.01" value="{{ old('minimum_offer', $domain->minimum_offer) }}">
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500">Set the lowest offer you're willing to consider.</p>
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
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Features</h3>
                                        <div>
                                            <label for="additional_features" class="block text-sm font-medium text-gray-700">Additional Features</label>
                                            <div class="mt-1">
                                                <textarea name="additional_features" id="additional_features" rows="3" class="block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('additional_features') border-red-500 @enderror" placeholder="Any additional features, SEO value, backlinks, etc...">{{ old('additional_features', $domain->additional_features) }}</textarea>
                                            </div>
                                            @error('additional_features')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                        <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Cancel
                                        </a>
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Update Domain') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

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

            // Initialize fields on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleBinFields();
                toggleOfferFields();
                toggleBiddingFields();
            });
        </script>
    </body>
</html>
