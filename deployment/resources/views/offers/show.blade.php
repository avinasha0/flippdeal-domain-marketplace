<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlippDeal') }} - Offer Details</title>

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
                    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Page Header -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900">Offer Details</h1>
                                    <p class="mt-2 text-gray-600">Review and manage your domain offer.</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('offers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        All Offers
                                    </a>
                                    <a href="{{ route('domains.show', $offer->domain) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                        </svg>
                                        View Domain
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Main Content -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Offer Status Card -->
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h2 class="text-lg font-semibold text-gray-900">Offer Status</h2>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            @if($offer->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($offer->status === 'accepted') bg-green-100 text-green-800
                                            @elseif($offer->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($offer->status === 'withdrawn') bg-gray-100 text-gray-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($offer->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Offer Amount</span>
                                            <span class="text-lg font-bold text-purple-600">${{ number_format($offer->offer_amount, 2) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Domain</span>
                                            <span class="text-sm font-mono text-gray-900">{{ $offer->domain->full_domain }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Asking Price</span>
                                            <span class="text-sm text-gray-900">{{ $offer->domain->formatted_price }}</span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-600">Submitted</span>
                                            <span class="text-sm text-gray-900">{{ $offer->created_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                        
                                        @if($offer->expires_at)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <span class="text-sm font-medium text-gray-600">Expires</span>
                                                <span class="text-sm text-gray-900 {{ $offer->isExpired() ? 'text-red-600 font-medium' : '' }}">
                                                    {{ $offer->expires_at->format('M j, Y \a\t g:i A') }}
                                                    @if($offer->isExpired())
                                                        (Expired)
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Card -->
                                @if($offer->message)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Message to Seller</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-gray-700">{{ $offer->message }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Seller Response Card -->
                                @if($offer->seller_response)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Seller Response</h3>
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <p class="text-gray-700">{{ $offer->seller_response }}</p>
                                        <p class="text-sm text-gray-500 mt-2">
                                            Responded on {{ $offer->responded_at->format('M j, Y \a\t g:i A') }}
                                        </p>
                                    </div>
                                </div>
                                @endif

                                <!-- Action Buttons -->
                                @if($offer->status === 'pending')
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                                    <div class="flex flex-wrap gap-3">
                                        @if($offer->canBeWithdrawn())
                                        <form method="POST" action="{{ route('offers.destroy', $offer) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Withdraw Offer
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Sidebar -->
                            <div class="space-y-6">
                                <!-- Domain Info -->
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Domain Information</h3>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Domain Name</span>
                                            <p class="text-sm text-gray-900">{{ $offer->domain->full_domain }}</p>
                                        </div>
                                        
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Category</span>
                                            <p class="text-sm text-gray-900">{{ ucfirst($offer->domain->category ?? 'General') }}</p>
                                        </div>
                                        
                                        @if($offer->domain->description)
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Description</span>
                                            <p class="text-sm text-gray-900">{{ Str::limit($offer->domain->description, 100) }}</p>
                                        </div>
                                        @endif
                                        
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Listed by</span>
                                            <p class="text-sm text-gray-900">{{ $offer->domain->user->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Offer Timeline -->
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">Offer Submitted</p>
                                                <p class="text-sm text-gray-500">{{ $offer->created_at->format('M j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($offer->responded_at)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">Seller Responded</p>
                                                <p class="text-sm text-gray-500">{{ $offer->responded_at->format('M j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($offer->expires_at)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-8 h-8 {{ $offer->isExpired() ? 'bg-red-100' : 'bg-yellow-100' }} rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 {{ $offer->isExpired() ? 'text-red-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">Offer Expires</p>
                                                <p class="text-sm text-gray-500">{{ $offer->expires_at->format('M j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
