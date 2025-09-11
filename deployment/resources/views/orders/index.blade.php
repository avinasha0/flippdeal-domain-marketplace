<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlippDeal') }} - My Orders</title>

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
                            <a href="{{ route('my.domains.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">My Domains</a>
                            <a href="{{ route('orders.index') }}" class="text-purple-600 border-b-2 border-purple-600 px-3 py-2 text-sm font-medium">My Orders</a>
                            <a href="{{ route('offers.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Offers</a>
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
                    <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
                    <p class="mt-2 text-gray-600">Track your domain purchases and sales</p>
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

                <!-- Orders List -->
                @if($orders->count() > 0)
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Order Summary</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">All your domain orders and transactions</p>
                        </div>
                        
                        <div class="border-t border-gray-200">
                            @foreach($orders as $order)
                                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 last:border-b-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="text-lg font-medium text-gray-900">
                                                        Order #{{ $order->order_number }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ $order->domain->full_domain }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold text-green-600">
                                                        {{ $order->formatted_total_amount }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        @if($order->buyer_id === Auth::id())
                                                            You paid
                                                        @else
                                                            You receive
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                                    <p class="mt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            @if($order->status === 'completed') bg-green-100 text-green-800
                                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                                                            @elseif($order->status === 'in_escrow') bg-purple-100 text-purple-800
                                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                            @elseif($order->status === 'disputed') bg-orange-100 text-orange-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                        </span>
                                                    </p>
                                                </div>
                                                
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Role</p>
                                                    <p class="mt-1 text-sm text-gray-900">
                                                        @if($order->buyer_id === Auth::id())
                                                            Buyer
                                                        @else
                                                            Seller
                                                        @endif
                                                    </p>
                                                </div>
                                                
                                                <div>
                                                    <p class="text-sm font-medium text-gray-500">Created</p>
                                                    <p class="mt-1 text-sm text-gray-900">
                                                        {{ $order->created_at->format('M j, Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            @if($order->buyer_id === Auth::id())
                                                <div class="mt-4">
                                                    <p class="text-sm text-gray-500">Commission</p>
                                                    <p class="text-sm text-gray-900">{{ $order->formatted_commission_amount }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="ml-6 flex flex-col space-y-2">
                                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                View Details
                                            </a>
                                            
                                            @if($order->buyer_id === Auth::id() && $order->status === 'in_escrow')
                                                <form method="POST" action="{{ route('orders.complete', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Complete Order
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($order->status === 'pending' && $order->buyer_id === Auth::id())
                                                <a href="{{ route('orders.payment', $order) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    Pay Now
                                                </a>
                                            @endif
                                            
                                            @if(in_array($order->status, ['paid', 'in_escrow']) && ($order->buyer_id === Auth::id() || $order->seller_id === Auth::id()))
                                                <form method="POST" action="{{ route('orders.dispute', $order) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                                        Raise Dispute
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by browsing domains and making your first purchase.</p>
                        <div class="mt-6">
                            <a href="{{ route('domains.public.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Browse Domains
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
