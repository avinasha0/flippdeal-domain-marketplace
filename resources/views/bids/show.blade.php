@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Bid Details</h1>
            <p class="mt-2 text-gray-600">View your bid information for this domain auction</p>
        </div>

        <!-- Success Message -->
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

        <!-- Domain Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $bid->domain->full_domain }}</h2>
                    <p class="text-gray-600">{{ $bid->domain->description ?: 'No description available' }}</p>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span>Category: {{ $bid->domain->category ?: 'Uncategorized' }}</span>
                        <span>•</span>
                        <span>Asking Price: {{ $bid->domain->formatted_price }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-purple-600">
                        {{ $bid->formatted_amount }}
                    </div>
                    <div class="text-sm text-gray-500">Your Current Bid</div>
                </div>
            </div>
        </div>

        <!-- Bid Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Bid Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $bid->formatted_amount }}</div>
                    <div class="text-sm text-purple-700">Bid Amount</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $bid->formatted_max_auto_bid }}</div>
                    <div class="text-sm text-blue-700">Max Auto-Bid</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $bid->time_ago }}</div>
                    <div class="text-sm text-green-700">Bid Placed</div>
                </div>
            </div>
            
            @if($bid->domain->reserve_price)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Reserve Price:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $bid->domain->formatted_reserve_price }}</span>
                    </div>
                    <div class="mt-1">
                        @if($bid->domain->reserve_met)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Reserve Met
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                ⚠️ Reserve Not Met
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Bid Details -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bid Information</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Bid Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($bid->status === 'active') bg-green-100 text-green-800
                        @elseif($bid->status === 'outbid') bg-red-100 text-red-800
                        @elseif($bid->status === 'won') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($bid->status) }}
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Bid Amount</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $bid->formatted_amount }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Maximum Auto-Bid</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $bid->formatted_max_auto_bid }}</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Bid Placed</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $bid->bid_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
                
                @if($bid->outbid_at)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-600">Outbid At</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $bid->outbid_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
                @endif
                
                @if($bid->bidder_note)
                <div class="p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-blue-900">Note to Seller:</span>
                    <p class="mt-1 text-sm text-blue-800">{{ $bid->bidder_note }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Auction Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Auction Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $bid->domain->bid_count }}</div>
                    <div class="text-sm text-purple-700">Total Bids</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $bid->domain->formatted_next_minimum_bid }}</div>
                    <div class="text-sm text-blue-700">Next Min Bid</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">{{ $bid->domain->auction_time_remaining }}</div>
                    <div class="text-sm text-orange-700">Time Remaining</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="flex flex-wrap gap-4">
                @if($bid->status === 'active')
                    <span class="bg-blue-100 text-blue-800 px-6 py-3 rounded-lg font-semibold">
                        Active Bid
                    </span>
                @else
                    <span class="bg-gray-100 text-gray-800 px-6 py-3 rounded-lg font-semibold">
                        {{ ucfirst($bid->status) }} Bid
                    </span>
                @endif
                
                <a href="{{ route('domains.bids.index', $bid->domain) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                    View All Bids
                </a>
                
                <a href="{{ route('domains.show', $bid->domain) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                    Back to Domain
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
