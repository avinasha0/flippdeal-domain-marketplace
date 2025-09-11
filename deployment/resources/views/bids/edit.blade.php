@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Your Bid</h1>
            <p class="mt-2 text-gray-600">Update your bid settings for this domain auction</p>
        </div>

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

        <!-- Edit Form -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Your Bid</h3>
            
            <form method="POST" action="{{ route('bids.update', $bid) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Bid Amount -->
                <div>
                    <label for="bid_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Bid Amount
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" 
                               name="bid_amount" 
                               id="bid_amount" 
                               step="0.01" 
                               min="{{ $bid->domain->starting_bid }}"
                               value="{{ old('bid_amount', $bid->bid_amount) }}"
                               class="block w-full pl-7 border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('bid_amount') border-red-500 @enderror" 
                               placeholder="0.00">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Enter your new bid amount. Must be at least {{ $bid->domain->formatted_starting_bid }}.
                    </p>
                    @error('bid_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Auto-Bid Amount -->
                <div>
                    <label for="max_auto_bid" class="block text-sm font-medium text-gray-700 mb-2">
                        Maximum Auto-Bid Amount
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" 
                               name="max_auto_bid" 
                               id="max_auto_bid" 
                               step="0.01" 
                               min="{{ old('bid_amount', $bid->bid_amount) + 0.01 }}"
                               value="{{ old('max_auto_bid', $bid->max_auto_bid) }}"
                               class="block w-full pl-7 border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('max_auto_bid') border-red-500 @enderror" 
                               placeholder="0.00">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Set a maximum amount for automatic bidding. Must be higher than your bid amount.
                    </p>
                    @error('max_auto_bid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bidder Note -->
                <div>
                    <label for="bidder_note" class="block text-sm font-medium text-gray-700 mb-2">
                        Note to Seller (Optional)
                    </label>
                    <textarea name="bidder_note" 
                              id="bidder_note" 
                              rows="3" 
                              class="block w-full border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-md @error('bidder_note') border-red-500 @enderror" 
                              placeholder="Add a personal message or reason for your bid...">{{ old('bidder_note', $bid->bidder_note) }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">
                        This note will be visible to the domain owner.
                    </p>
                    @error('bidder_note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit and Cancel -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('domains.bids.index', $bid->domain) }}" 
                           class="text-blue-600 hover:text-blue-500 font-medium">
                            ← Back to Bids
                        </a>
                        
                        <div class="flex space-x-3">
                                                         <a href="{{ route('domains.show', $bid->domain) }}" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                                 Cancel
                             </a>
                            
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                                Update Bid
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bidding Guidelines -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bid Update Guidelines</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>You can edit your bid amount, maximum auto-bid amount, and bidder note.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Maximum auto-bid must be higher than your current bid amount.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Auto-bidding will automatically place bids up to your maximum amount to keep you in the lead.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>You can cancel your bid at any time before the auction ends if you're no longer the highest bidder.</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
