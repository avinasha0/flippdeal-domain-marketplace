@props(['domain', 'showFullInfo' => true])

@php
    $auctionEndsAt = $domain->auction_ends_at ?? $domain->created_at->addDays(7);
    $isEnded = $auctionEndsAt < now();
    $timeRemaining = $isEnded ? 0 : $auctionEndsAt->diffInSeconds(now());
    $isEndingSoon = $timeRemaining > 0 && $timeRemaining <= 300; // 5 minutes
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4" 
     x-data="auctionCountdown({{ $timeRemaining }}, '{{ $auctionEndsAt->toISOString() }}')">
    
    @if($isEnded)
        <!-- Auction Ended -->
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-1">Auction Ended</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Ended {{ $auctionEndsAt->diffForHumans() }}
            </p>
            @if($showFullInfo)
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                    {{ $auctionEndsAt->format('M j, Y \a\t g:i A T') }}
                </p>
            @endif
        </div>
    @else
        <!-- Active Auction -->
        <div class="text-center">
            @if($isEndingSoon)
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-orange-600 dark:text-orange-400 mb-1">Ending Soon!</h3>
            @else
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-blue-600 dark:text-blue-400 mb-1">Auction Active</h3>
            @endif
            
            <!-- Countdown Timer -->
            <div class="mb-3">
                <div class="text-2xl font-mono font-bold {{ $isEndingSoon ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-white' }}" 
                     x-text="timeDisplay">
                    {{ $timeRemaining > 0 ? gmdate('H:i:s', $timeRemaining) : '00:00:00' }}
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span x-text="timeLabel">Time Remaining</span>
                </p>
            </div>

            @if($showFullInfo)
                <!-- End Time Info -->
                <div class="text-xs text-gray-500 dark:text-gray-500 space-y-1">
                    <p>Ends: <span x-text="endTimeLocal">{{ $auctionEndsAt->format('M j, Y \a\t g:i A T') }}</span></p>
                    <p>UTC: <span x-text="endTimeUTC">{{ $auctionEndsAt->utc()->format('M j, Y \a\t g:i A') }} UTC</span></p>
                </div>
            @endif

            <!-- Current Bid Info -->
            @if($domain->currentBid)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Current Bid</div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        ${{ number_format($domain->currentBid->amount, 2) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        by {{ $domain->currentBid->bidder->name }}
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4 space-y-2">
                @auth
                    @if(auth()->id() !== $domain->user_id)
                        <button @click="placeBid({{ $domain->id }})" 
                                class="w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            Place Bid
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors text-center">
                        Login to Bid
                    </a>
                @endauth
                
                <button @click="watchAuction({{ $domain->id }})" 
                        class="w-full px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                    <span x-text="isWatching ? 'Unwatch' : 'Watch'">Watch</span>
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function auctionCountdown(initialTime, endTimeISO) {
    return {
        timeRemaining: initialTime,
        endTimeISO: endTimeISO,
        isWatching: false,
        interval: null,

        get timeDisplay() {
            if (this.timeRemaining <= 0) return '00:00:00';
            
            const hours = Math.floor(this.timeRemaining / 3600);
            const minutes = Math.floor((this.timeRemaining % 3600) / 60);
            const seconds = this.timeRemaining % 60;
            
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },

        get timeLabel() {
            if (this.timeRemaining <= 0) return 'Auction Ended';
            if (this.timeRemaining <= 300) return 'Ending Soon!';
            if (this.timeRemaining <= 3600) return 'Less than 1 hour';
            return 'Time Remaining';
        },

        get endTimeLocal() {
            return new Date(this.endTimeISO).toLocaleString();
        },

        get endTimeUTC() {
            return new Date(this.endTimeISO).toUTCString();
        },

        init() {
            if (this.timeRemaining > 0) {
                this.interval = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.interval);
                        this.timeRemaining = 0;
                        // Reload page to show ended state
                        setTimeout(() => window.location.reload(), 1000);
                    }
                }, 1000);
            }
        },

        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        },

        async placeBid(domainId) {
            // This would open a bid modal or redirect to bid page
            window.location.href = `/domains/${domainId}/bid`;
        },

        async watchAuction(domainId) {
            try {
                const response = await fetch(`/api/domains/${domainId}/watch`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                if (response.ok) {
                    this.isWatching = !this.isWatching;
                } else {
                    alert('Failed to update watch status. Please try again.');
                }
            } catch (error) {
                console.error('Error updating watch status:', error);
                alert('An error occurred. Please try again.');
            }
        }
    }
}
</script>
