@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Place a Bid</h1>
            <p class="mt-2 text-gray-600">Submit your bid for this domain auction</p>
        </div>

        <!-- Domain Summary -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $domain->full_domain }}</h2>
                    <p class="text-gray-600">{{ $domain->description ?: 'No description available' }}</p>
                    <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span>Category: {{ $domain->category ?: 'Uncategorized' }}</span>
                        <span>•</span>
                        <span>Asking Price: {{ $domain->formatted_price }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-purple-600">
                        {{ $domain->formatted_current_bid ?: $domain->formatted_starting_bid }}
                    </div>
                    <div class="text-sm text-gray-500">
                        @if($domain->current_bid)
                            Current Bid
                        @else
                            Starting Bid
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Auction Information -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Auction Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $domain->bid_count }}</div>
                    <div class="text-sm text-purple-700">Total Bids</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $domain->formatted_next_minimum_bid }}</div>
                    <div class="text-sm text-blue-700">Next Min Bid</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <div class="text-2xl font-bold text-orange-600">{{ $domain->auction_time_remaining }}</div>
                    <div class="text-sm text-orange-700">Time Remaining</div>
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

        <!-- Bid Form -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Place Your Bid</h3>
            
            @if($userHighestBid)
                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm text-blue-800">
                            You currently have the highest bid at <strong>{{ $userHighestBid->formatted_amount }}</strong>
                        </span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('domains.bids.store', $domain) }}" class="space-y-6">
                @csrf
                
                <!-- Bid Amount -->
                <div>
                    <label for="bid_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Bid Amount ($)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" 
                               name="bid_amount" 
                               id="bid_amount" 
                               class="block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('bid_amount') border-red-500 @enderror" 
                               placeholder="{{ $domain->next_minimum_bid }}" 
                               min="{{ $domain->next_minimum_bid }}" 
                               step="0.01" 
                               value="{{ old('bid_amount') }}" 
                               required>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Your bid must be at least <strong>{{ $domain->formatted_next_minimum_bid }}</strong>
                    </p>
                    @error('bid_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Auto-Bid Option -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" 
                               name="is_auto_bid" 
                               id="is_auto_bid" 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" 
                               {{ old('is_auto_bid') ? 'checked' : '' }}
                               onchange="toggleAutoBidFields()">
                        <label for="is_auto_bid" class="ml-2 block text-sm font-medium text-gray-900">
                            Enable Auto-Bidding
                        </label>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">
                        Set a maximum bid amount and we'll automatically bid for you up to that limit.
                    </p>
                    
                    <div id="auto_bid_fields" class="hidden">
                        <label for="max_auto_bid" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Auto-Bid Amount ($)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" 
                                   name="max_auto_bid" 
                                   id="max_auto_bid" 
                                   class="block w-full pl-7 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('max_auto_bid') border-red-500 @enderror" 
                                   placeholder="1000" 
                                   min="{{ $domain->next_minimum_bid + 1 }}" 
                                   step="0.01" 
                                   value="{{ old('max_auto_bid') }}">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            This is the maximum amount you're willing to pay. We'll bid automatically up to this limit.
                        </p>
                        @error('max_auto_bid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bidder Note -->
                <div>
                    <label for="bidder_note" class="block text-sm font-medium text-gray-700 mb-2">
                        Note to Seller (Optional)
                    </label>
                    <textarea name="bidder_note" 
                              id="bidder_note" 
                              rows="3" 
                              class="block w-full border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-md @error('bidder_note') border-red-500 @enderror" 
                              placeholder="Add a personal message or reason for your bid...">{{ old('bidder_note') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">
                        This note will be visible to the domain owner.
                    </p>
                    @error('bidder_note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms and Submit -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" 
                               name="terms_accepted" 
                               id="terms_accepted" 
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" 
                               required>
                        <label for="terms_accepted" class="ml-2 block text-sm text-gray-700">
                            I understand that placing a bid is a binding commitment and I may be obligated to complete the purchase if I win.
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('domains.show', $domain) }}" 
                           class="text-purple-600 hover:text-purple-500 font-medium">
                            ← Back to Domain
                        </a>
                        
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg">
                            Place Bid
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bidding Guidelines -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-200 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bidding Guidelines</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Bids are binding commitments. Only bid what you're willing to pay.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Each bid must be at least {{ $domain->formatted_minimum_bid_increment }} higher than the previous bid.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>If you win, you'll be contacted within 24 hours to complete the purchase.</span>
                </div>
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-purple-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>You can cancel your bid before the auction ends if you're no longer the highest bidder.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAutoBidFields() {
        const isAutoBid = document.getElementById('is_auto_bid');
        const autoBidFields = document.getElementById('auto_bid_fields');
        const maxAutoBidInput = document.getElementById('max_auto_bid');
        
        if (isAutoBid.checked) {
            autoBidFields.classList.remove('hidden');
            maxAutoBidInput.required = true;
        } else {
            autoBidFields.classList.add('hidden');
            maxAutoBidInput.required = false;
            maxAutoBidInput.value = '';
        }
    }

    // Initialize fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleAutoBidFields();
    });
</script>
@endsection
