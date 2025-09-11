<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlippDeal') }} - {{ $domain->full_domain }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Custom Header -->
        <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
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

                        <!-- Navigation -->
                        <nav class="flex space-x-8">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Dashboard</a>
                            <a href="{{ route('domains.public.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Browse Domains</a>
                            @auth
                                <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">My Orders</a>
                                <a href="{{ route('offers.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Offers</a>
                            @endauth
                        </nav>
                    </div>

                    <!-- Auth Section -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="ml-2 text-gray-700 font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                                </button>

                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Sign In</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">Sign Up</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Domain Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900 font-mono">{{ $domain->full_domain }}</h1>
                            <p class="mt-2 text-xl text-gray-600">Listed by {{ $domain->user->name }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @auth
                                @if(Auth::id() !== $domain->user_id)
                                    <!-- Favorite Button -->
                                    <button id="favorite-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span id="favorite-text">Add to Favorites</span>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Domain Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Domain Details Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Domain Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Category</p>
                                    <p class="text-sm text-gray-900">{{ $domain->category ? ucfirst($domain->category) : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Registration Date</p>
                                    <p class="text-sm text-gray-900">{{ $domain->registration_date ? $domain->registration_date->format('M j, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Expiry Date</p>
                                    <p class="text-sm text-gray-900">{{ $domain->expiry_date ? $domain->expiry_date->format('M j, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($domain->status === 'active') bg-green-100 text-green-800
                                            @elseif($domain->status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($domain->status === 'sold') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($domain->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            @if($domain->description)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-500">Description</p>
                                <p class="text-sm text-gray-900 mt-1">{{ $domain->description }}</p>
                            </div>
                            @endif

                            @if($domain->tags && count($domain->tags) > 0)
                            <div class="mt-6">
                                <p class="text-sm font-medium text-gray-500">Tags</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($domain->tags as $tag)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Domain Features -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Domain Features</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-5 h-5 {{ $domain->has_website ? 'text-green-500' : 'text-gray-400' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900">Has Website</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-5 h-5 {{ $domain->has_traffic ? 'text-green-500' : 'text-gray-400' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900">Has Traffic</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-5 h-5 {{ $domain->premium_domain ? 'text-green-500' : 'text-gray-400' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900">Premium Domain</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-5 h-5 {{ $domain->domain_verified ? 'text-green-500' : 'text-gray-400' }}">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-900">Verified Ownership</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar - Pricing & Actions -->
                    <div class="space-y-6">
                        <!-- Pricing Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">Asking Price</p>
                                <p class="text-4xl font-bold text-green-600 mt-1">${{ number_format($domain->asking_price, 2) }}</p>
                                
                                @if($domain->hasBin())
                                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm font-medium text-blue-900">Buy It Now Price</p>
                                    <p class="text-2xl font-bold text-blue-600">${{ number_format($domain->bin_price, 2) }}</p>
                                </div>
                                @endif

                                @if($domain->minimum_offer)
                                <div class="mt-3">
                                    <p class="text-sm text-gray-500">Minimum Offer</p>
                                    <p class="text-lg font-semibold text-gray-900">${{ number_format($domain->minimum_offer, 2) }}</p>
                                </div>
                                @endif
                            </div>

                            @auth
                                @if(Auth::id() !== $domain->user_id)
                                    <div class="mt-6 space-y-3">
                                        <!-- Buy Now Button -->
                                        @if($domain->hasBin())
                                        <form method="POST" action="{{ route('domains.buy', $domain) }}" class="w-full">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="stripe">
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                                </svg>
                                                Buy Now for ${{ number_format($domain->bin_price, 2) }}
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Make Offer Button -->
                                        @if($domain->acceptsOffers())
                                        <button @click="$dispatch('open-modal', 'make-offer')" class="w-full inline-flex items-center justify-center px-4 py-3 border border-purple-300 text-base font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            Make Offer
                                        </button>
                                        @endif

                                        <!-- Contact Seller Button -->
                                        <button @click="$dispatch('open-modal', 'contact-seller')" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            Contact Seller
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="mt-6 space-y-3">
                                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Sign In to Make Offer
                                    </a>
                                    <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Create Account
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- Seller Information -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Seller Information</h3>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-medium text-lg">{{ substr($domain->user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $domain->user->name }}</p>
                                    <p class="text-sm text-gray-500">Member since {{ $domain->user->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Make Offer Modal -->
        <div x-data="{ show: false, offerAmount: '', message: '', expiresInDays: 7 }" 
             @open-modal.window="if ($event.detail === 'make-offer') show = true"
             x-show="show" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="show" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="show" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Make an Offer</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Make an offer on {{ $domain->full_domain }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('offers.store') }}" class="mt-5 sm:mt-6">
                        @csrf
                        <input type="hidden" name="domain_id" value="{{ $domain->id }}">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="offer_amount" class="block text-sm font-medium text-gray-700">Offer Amount ($)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" 
                                           name="offer_amount" 
                                           id="offer_amount" 
                                           x-model="offerAmount"
                                           step="0.01" 
                                           min="0.01" 
                                           required
                                           class="focus:ring-purple-500 focus:border-purple-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" 
                                           placeholder="0.00">
                                </div>
                                @if($domain->minimum_offer)
                                <p class="mt-1 text-sm text-gray-500">Minimum offer: ${{ number_format($domain->minimum_offer, 2) }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                                <textarea name="message" 
                                          id="message" 
                                          x-model="message"
                                          rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" 
                                          placeholder="Tell the seller why you're interested in this domain..."></textarea>
                            </div>

                            <div>
                                <label for="expires_in_days" class="block text-sm font-medium text-gray-700">Offer Expires In</label>
                                <select name="expires_in_days" 
                                        id="expires_in_days" 
                                        x-model="expiresInDays"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option value="1">1 day</option>
                                    <option value="3">3 days</option>
                                    <option value="7" selected>7 days</option>
                                    <option value="14">14 days</option>
                                    <option value="30">30 days</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:col-start-2 sm:text-sm">
                                Submit Offer
                            </button>
                            <button @click="show = false" 
                                    type="button" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Seller Modal -->
        <div x-data="{ show: false, subject: '', message: '' }" 
             @open-modal.window="if ($event.detail === 'contact-seller') show = true"
             x-show="show" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="show" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="show" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Contact Seller</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Send a message to {{ $domain->user->name }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('messages.store') }}" class="mt-5 sm:mt-6">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $domain->user_id }}">
                        <input type="hidden" name="domain_id" value="{{ $domain->id }}">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" 
                                       name="subject" 
                                       id="subject" 
                                       x-model="subject"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" 
                                       placeholder="Inquiry about {{ $domain->full_domain }}">
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                                <textarea name="message" 
                                          id="message" 
                                          x-model="message"
                                          rows="4" 
                                          required
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" 
                                          placeholder="Tell the seller what you'd like to know..."></textarea>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                Send Message
                            </button>
                            <button @click="show = false" 
                                    type="button" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Favorite Functionality Script -->
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteBtn = document.getElementById('favorite-btn');
            const favoriteText = document.getElementById('favorite-text');
            
            if (favoriteBtn) {
                // Check if domain is already favorited
                fetch(`/favorites/check?domain_id={{ $domain->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_favorite) {
                            favoriteBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
                            favoriteBtn.classList.add('border-purple-300', 'bg-purple-50', 'text-purple-700');
                            favoriteText.textContent = 'Remove from Favorites';
                        }
                    });

                // Toggle favorite
                favoriteBtn.addEventListener('click', function() {
                    const isFavorited = favoriteBtn.classList.contains('bg-purple-50');
                    
                    if (isFavorited) {
                        // Remove from favorites
                        fetch('/favorites/remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                domain_id: {{ $domain->id }}
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                favoriteBtn.classList.remove('border-purple-300', 'bg-purple-50', 'text-purple-700');
                                favoriteBtn.classList.add('border-gray-300', 'bg-white', 'text-gray-700');
                                favoriteText.textContent = 'Add to Favorites';
                            }
                        });
                    } else {
                        // Add to favorites
                        fetch('/favorites/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                domain_id: {{ $domain->id }}
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                favoriteBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-700');
                                favoriteBtn.classList.add('border-purple-300', 'bg-purple-50', 'text-purple-700');
                                favoriteText.textContent = 'Remove from Favorites';
                            }
                        });
                    }
                });
            }
        });
    </script>
    @endauth
</body>
</html>
