@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bid History</h1>
                    <p class="mt-2 text-gray-600">All bids for {{ $domain->full_domain }}</p>
                </div>
                <a href="{{ route('domains.show', $domain) }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-700 transition duration-200">
                    ← Back to Domain
                </a>
            </div>
        </div>

        <!-- Domain Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $domain->formatted_starting_bid }}</div>
                    <div class="text-sm text-gray-600">Starting Bid</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $domain->formatted_current_bid ?: 'No bids' }}</div>
                    <div class="text-sm text-gray-600">Current Bid</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $domain->bid_count }}</div>
                    <div class="text-sm text-gray-600">Total Bids</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $domain->auction_time_remaining }}</div>
                    <div class="text-sm text-gray-600">Time Remaining</div>
                </div>
            </div>
            
            @if($domain->reserve_price)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Reserve Price:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $domain->formatted_reserve_price }}</span>
                    </div>
                    <div class="mt-1">
                        @if($domain->reserve_met)
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

        <!-- Bids Table -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">All Bids</h3>
            </div>
            
            @if($bids->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bidder
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bids as $bid)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $bid->bidder->name }}
                                            </div>
                                            @if($bid->bidder_id === auth()->id())
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    You
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ $bid->formatted_amount }}
                                        </div>
                                        @if($bid->is_auto_bid && $bid->max_auto_bid)
                                            <div class="text-xs text-gray-500">
                                                Max: {{ $bid->formatted_max_auto_bid }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($bid->status === 'active')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Highest Bid
                                            </span>
                                        @elseif($bid->status === 'outbid')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Outbid
                                            </span>
                                        @elseif($bid->status === 'won')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Won
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($bid->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $bid->time_ago }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($bid->is_auto_bid)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Auto-Bid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($bid->bidder_id === auth()->id() && $bid->status === 'active')
                                            <div class="flex space-x-2">
                                                <a href="{{ route('bids.edit', $bid) }}" 
                                                   class="text-purple-600 hover:text-purple-900">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('bids.destroy', $bid) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Are you sure you want to cancel this bid?')">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <a href="{{ route('bids.show', $bid) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bids->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No bids yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Be the first to place a bid on this domain!</p>
                    <div class="mt-6">
                        <a href="{{ route('domains.bids.create', $domain) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Place First Bid
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Place Bid Button -->
        @auth
            @if(auth()->id() !== $domain->user_id && $domain->isAuctionActive())
                <div class="mt-8 text-center">
                    <a href="{{ route('domains.bids.create', $domain) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg">
                        Place a Bid
                    </a>
                </div>
            @endif
        @endauth
    </div>
</div>
@endsection
