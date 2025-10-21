<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlippDeal') }} - My Offers</title>

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
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">My Orders</a>
                            <a href="{{ route('offers.index') }}" class="text-purple-600 border-b-2 border-purple-600 px-3 py-2 text-sm font-medium">Offers</a>
                            <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Messages</a>
                            <a href="{{ route('favorites.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Favorites</a>
                        </nav>
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
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">My Offers</h1>
                            <p class="mt-2 text-gray-600">Manage your domain offers and negotiations</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('offers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Make New Offer
                            </a>
                        </div>
                    </div>
                </div>

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

                <!-- Tabs -->
                <div class="mb-8" x-data="{ activeTab: 'received' }">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'received'" :class="activeTab === 'received' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                Offers Received ({{ $receivedOffers->total() }})
                            </button>
                            <button @click="activeTab = 'sent'" :class="activeTab === 'sent' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                Offers Sent ({{ $myOffers->total() }})
                            </button>
                        </nav>
                    </div>

                    <!-- Received Offers Tab -->
                    <div x-show="activeTab === 'received'" class="mt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Offers for Your Domains</h2>
                        
                        @if($receivedOffers->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                @foreach($receivedOffers as $offer)
                                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 last:border-b-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h4 class="text-lg font-medium text-gray-900">
                                                            {{ $offer->domain->full_domain }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            Offer from {{ $offer->buyer->name }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-2xl font-bold text-green-600">
                                                            ${{ number_format($offer->offer_amount, 2) }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            @if($offer->domain->asking_price)
                                                                {{ round((($offer->offer_amount - $offer->domain->asking_price) / $offer->domain->asking_price) * 100, 1) }}% {{ $offer->offer_amount > $offer->domain->asking_price ? 'above' : 'below' }} asking price
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Status</p>
                                                        <p class="mt-1">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                @if($offer->status === 'accepted') bg-green-100 text-green-800
                                                                @elseif($offer->status === 'pending') bg-yellow-100 text-yellow-800
                                                                @elseif($offer->status === 'rejected') bg-red-100 text-red-800
                                                                @elseif($offer->status === 'expired') bg-gray-100 text-gray-800
                                                                @elseif($offer->status === 'withdrawn') bg-gray-100 text-gray-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ ucfirst($offer->status) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Your Asking Price</p>
                                                        <p class="mt-1 text-sm text-gray-900">${{ number_format($offer->domain->asking_price, 2) }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Received</p>
                                                        <p class="mt-1 text-sm text-gray-900">
                                                            {{ $offer->created_at->format('M j, Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                @if($offer->message)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-500">Buyer's Message</p>
                                                    <p class="text-sm text-gray-900 mt-1">{{ $offer->message }}</p>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="ml-6 flex flex-col space-y-2">
                                                <a href="{{ route('offers.show', $offer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    View Details
                                                </a>
                                                
                                                @if($offer->status === 'pending')
                                                    <button @click="$dispatch('open-modal', { action: 'accept', offerId: {{ $offer->id }} })" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Accept Offer
                                                    </button>
                                                    
                                                    <button @click="$dispatch('open-modal', { action: 'reject', offerId: {{ $offer->id }} })" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        Reject Offer
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $receivedOffers->links() }}
                            </div>
                        @else
                            <div class="text-center py-12 bg-white rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No offers received yet</h3>
                                <p class="mt-1 text-sm text-gray-500">When someone makes an offer on your domains, it will appear here.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Sent Offers Tab -->
                    <div x-show="activeTab === 'sent'" class="mt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Offers You've Made</h2>
                        
                        @if($myOffers->count() > 0)
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                @foreach($myOffers as $offer)
                                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 last:border-b-0">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <h4 class="text-lg font-medium text-gray-900">
                                                            {{ $offer->domain->full_domain }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            Listed by {{ $offer->domain->user->name }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-2xl font-bold text-green-600">
                                                            ${{ number_format($offer->offer_amount, 2) }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            @if($offer->domain->asking_price)
                                                                {{ round((($offer->offer_amount - $offer->domain->asking_price) / $offer->domain->asking_price) * 100, 1) }}% {{ $offer->offer_amount > $offer->domain->asking_price ? 'above' : 'below' }} asking price
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Status</p>
                                                        <p class="mt-1">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                @if($offer->status === 'accepted') bg-green-100 text-green-800
                                                                @elseif($offer->status === 'pending') bg-yellow-100 text-yellow-800
                                                                @elseif($offer->status === 'rejected') bg-red-100 text-red-800
                                                                @elseif($offer->status === 'expired') bg-gray-100 text-gray-800
                                                                @elseif($offer->status === 'withdrawn') bg-gray-100 text-gray-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ ucfirst($offer->status) }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Asking Price</p>
                                                        <p class="mt-1 text-sm text-gray-900">${{ number_format($offer->domain->asking_price, 2) }}</p>
                                                    </div>
                                                    
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-500">Sent</p>
                                                        <p class="mt-1 text-sm text-gray-900">
                                                            {{ $offer->created_at->format('M j, Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                @if($offer->message)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-500">Your Message</p>
                                                    <p class="text-sm text-gray-900 mt-1">{{ $offer->message }}</p>
                                                </div>
                                                @endif

                                                @if($offer->seller_response)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-500">Seller's Response</p>
                                                    <p class="text-sm text-gray-900 mt-1">{{ $offer->seller_response }}</p>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            <div class="ml-6 flex flex-col space-y-2">
                                                <a href="{{ route('offers.show', $offer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    View Details
                                                </a>
                                                
                                                @if($offer->status === 'pending')
                                                    <a href="{{ route('offers.edit', $offer) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                        Edit Offer
                                                    </a>
                                                    
                                                    <form method="POST" action="{{ route('offers.destroy', $offer) }}" class="inline" onsubmit="return confirm('Are you sure you want to withdraw this offer?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            Withdraw
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($offer->status === 'accepted')
                                                    <a href="{{ route('offers.convert', $offer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Convert to Order
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $myOffers->links() }}
                            </div>
                        @else
                            <div class="text-center py-12 bg-white rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No offers sent yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Start making offers on domains you're interested in.</p>
                                <div class="mt-6">
                                    <a href="{{ route('domains.public.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Browse Domains
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>

        <!-- Accept/Reject Offer Modal -->
        <div x-data="{ show: false, action: '', offerId: null, responseMessage: '' }" 
             @open-modal.window="show = true; action = $event.detail.action; offerId = $event.detail.offerId; responseMessage = ''"
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
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" 
                             :class="action === 'accept' ? 'bg-green-100' : 'bg-red-100'">
                            <svg class="h-6 w-6" 
                                 :class="action === 'accept' ? 'text-green-600' : 'text-red-600'" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path x-show="action === 'accept'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                <path x-show="action === 'reject'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="action === 'accept' ? 'Accept Offer' : 'Reject Offer'"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    <span x-text="action === 'accept' ? 'Are you sure you want to accept this offer? This will create an order.' : 'Are you sure you want to reject this offer?'"></span>
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="response-message" class="block text-sm font-medium text-gray-700">Response Message (Optional)</label>
                                <textarea id="response-message" 
                                          x-model="responseMessage"
                                          rows="3" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" 
                                          placeholder="Add a message for the buyer..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <form :action="`/offers/${offerId}/${action}`" method="POST" class="sm:col-start-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="response_message" x-model="responseMessage">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:col-start-2 sm:text-sm"
                                    :class="action === 'accept' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 'bg-red-600 hover:bg-red-700 focus:ring-red-500'"
                                    x-text="action === 'accept' ? 'Accept Offer' : 'Reject Offer'">
                            </button>
                        </form>
                        <button @click="show = false" 
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
