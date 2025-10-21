<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlippDeal') }} - Order #{{ $order->order_number }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link href="{{ asset('build/assets/app-BANQcGNA.css') }}" rel="stylesheet">
<script src="{{ asset('build/assets/app-DtCVKgHt.js') }}"></script>
    
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
                            <a href="{{ route('orders.index') }}" class="text-purple-600 border-b-2 border-purple-600 px-3 py-2 text-sm font-medium">My Orders</a>
                            <a href="{{ route('offers.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Offers</a>
                            <a href="{{ route('messages.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Messages</a>
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
                            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                            <p class="mt-2 text-gray-600">{{ $order->domain->full_domain }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Orders
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

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Order Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Order Status Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Order Status</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
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
                            </div>
                            
                            <!-- Status Timeline -->
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Order Created</p>
                                        <p class="text-sm text-gray-500">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>

                                @if($order->paid_at)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Payment Received</p>
                                        <p class="text-sm text-gray-500">{{ $order->paid_at->format('F j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($order->status === 'in_escrow')
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">In Escrow</p>
                                        <p class="text-sm text-gray-500">Funds held securely</p>
                                    </div>
                                </div>
                                @endif

                                @if($order->completed_at)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Completed</p>
                                        <p class="text-sm text-gray-500">{{ $order->completed_at->format('F j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Domain Information -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Domain Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Domain Name</p>
                                    <p class="text-sm text-gray-900 font-mono">{{ $order->domain->full_domain }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Category</p>
                                    <p class="text-sm text-gray-900">{{ $order->domain->category ? ucfirst($order->domain->category) : 'N/A' }}</p>
                                </div>
                                @if($order->domain->description)
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-medium text-gray-500">Description</p>
                                    <p class="text-sm text-gray-900">{{ $order->domain->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Financial Details -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Financial Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Domain Price</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->formatted_domain_price }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Commission ({{ $order->domain->commission_rate ?? 5.00 }}%)</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->formatted_commission_amount }}</span>
                                </div>
                                <hr class="border-gray-200">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-900">Total Amount</span>
                                    <span class="text-lg font-bold text-green-600">{{ $order->formatted_total_amount }}</span>
                                </div>
                                @if($order->seller_id === Auth::id())
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">You Receive</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $order->formatted_seller_amount }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                            <div class="flex flex-wrap gap-3">
                                @if($order->buyer_id === Auth::id() && $order->status === 'in_escrow')
                                    <form method="POST" action="{{ route('orders.complete', $order) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Complete Order
                                        </button>
                                    </form>
                                @endif

                                @if($order->status === 'pending' && $order->buyer_id === Auth::id())
                                    <a href="{{ route('orders.payment', $order) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        Pay Now
                                    </a>
                                @endif

                                @if(in_array($order->status, ['paid', 'in_escrow']) && ($order->buyer_id === Auth::id() || $order->seller_id === Auth::id()))
                                    <form method="POST" action="{{ route('orders.dispute', $order) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Raise Dispute
                                        </button>
                                    </form>
                                @endif

                                @if($order->canBeCancelled() && $order->buyer_id === Auth::id())
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel Order
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Order Summary -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Order Number</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Created</span>
                                    <span class="text-sm text-gray-900">{{ $order->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Payment Method</span>
                                    <span class="text-sm text-gray-900">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                                </div>
                                @if($order->payment_transaction_id)
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Transaction ID</span>
                                    <span class="text-sm text-gray-900 font-mono text-xs">{{ $order->payment_transaction_id }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Buyer</p>
                                    <p class="text-sm text-gray-900">{{ $order->buyer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->buyer->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Seller</p>
                                    <p class="text-sm text-gray-900">{{ $order->seller->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->seller->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Actions -->
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact</h3>
                            <div class="space-y-3">
                                @php
                                    $otherUser = $order->buyer_id === Auth::id() ? $order->seller : $order->buyer;
                                @endphp
                                <a href="{{ route('messages.conversation', $otherUser) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Message {{ $otherUser->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
