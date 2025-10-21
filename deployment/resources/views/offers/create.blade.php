<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlippDeal') }} - Make an Offer</title>

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
                                    <a href="{{ route('domains.public.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                            </svg>
                                            Browse All Domains
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
                    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                        <!-- Page Header -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900">Make an Offer</h1>
                                    <p class="mt-2 text-gray-600">Submit your offer for this domain.</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                        </svg>
                                        Back to Domain
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Domain Summary -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">{{ $domain->full_domain }}</h2>
                                    <p class="text-sm text-gray-600">Listed by {{ $domain->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-purple-600">{{ $domain->formatted_price }}</div>
                                    <div class="text-sm text-gray-500">Asking Price</div>
                                </div>
                            </div>
                            
                            @if($domain->description)
                                <p class="text-gray-700 mb-4">{{ $domain->description }}</p>
                            @endif

                            @if($domain->acceptsOffers() && $domain->minimum_offer)
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-blue-800">
                                            Minimum offer accepted: {{ $domain->formatted_minimum_offer }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Offer Form -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-8">
                                <form method="POST" action="{{ route('offers.store') }}" class="space-y-6">
                                    @csrf
                                    <input type="hidden" name="domain_id" value="{{ $domain->id }}">

                                    <!-- Offer Amount -->
                                    <div>
                                        <label for="offer_amount" class="block text-sm font-medium text-gray-700">Your Offer Amount ($)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="offer_amount" id="offer_amount" class="block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('offer_amount') border-red-500 @enderror" placeholder="Enter your offer amount" min="{{ $domain->minimum_offer ?? 0.01 }}" max="{{ $domain->asking_price - 0.01 }}" step="0.01" value="{{ old('offer_amount') }}" required>
                                        </div>
                                        @error('offer_amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-sm text-gray-500">
                                            @if($domain->minimum_offer)
                                                Your offer must be at least {{ $domain->formatted_minimum_offer }} and less than the asking price.
                                            @else
                                                Your offer must be less than the asking price. No minimum amount required.
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Message to Seller -->
                                    <div>
                                        <label for="message" class="block text-sm font-medium text-gray-700">Message to Seller (Optional)</label>
                                        <div class="mt-1">
                                            <textarea name="message" id="message" rows="4" class="block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('message') border-red-500 @enderror" placeholder="Tell the seller why you're interested in this domain and any additional information...">{{ old('message') }}</textarea>
                                        </div>
                                        @error('message')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-sm text-gray-500">Include any relevant information about your plans for the domain or your timeline.</p>
                                    </div>

                                    <!-- Offer Expiry -->
                                    <div>
                                        <label for="expires_at" class="block text-sm font-medium text-gray-700">Offer Expires (Optional)</label>
                                        <div class="mt-1">
                                            <input type="datetime-local" name="expires_at" id="expires_at" class="block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('expires_at') border-red-500 @enderror" value="{{ old('expires_at') }}">
                                        </div>
                                        @error('expires_at')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-sm text-gray-500">Set an expiration date for your offer. Leave blank for no expiration.</p>
                                    </div>

                                    <!-- Terms and Conditions -->
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" name="terms_accepted" id="terms_accepted" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded @error('terms_accepted') border-red-500 @enderror" required>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="terms_accepted" class="font-medium text-gray-700">I agree to the terms and conditions</label>
                                            <p class="text-gray-500">By submitting this offer, you agree to our terms of service and offer policies.</p>
                                            @error('terms_accepted')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                                        <a href="{{ route('domains.show', $domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Cancel
                                        </a>
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Submit Offer
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Offer Guidelines -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
                            <h3 class="text-lg font-medium text-blue-900 mb-4">Offer Guidelines</h3>
                            <ul class="space-y-2 text-sm text-blue-800">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Offers are binding and indicate serious interest in purchasing the domain.
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    The seller will review your offer and may accept, reject, or counter it.
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    You can withdraw your offer before the seller responds.
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    If your offer is accepted, you'll be notified and can proceed with the purchase.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
