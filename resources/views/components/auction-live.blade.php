<div x-data="auctionLive({{ $domain->id }})" 
     x-init="init()"
     class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    
    <!-- Auction Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $domain->full_domain }}</h2>
            <p class="text-gray-600 dark:text-gray-400">Live Auction</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400" 
                 x-text="formatCurrency(currentHighestBid)">
                ${{ number_format($domain->bids()->where('is_winning', true)->first()?->amount ?? $domain->starting_price, 2) }}
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Current Highest Bid</p>
        </div>
    </div>

    <!-- Countdown Timer -->
    <div class="mb-6">
        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
            <div class="flex items-center justify-center space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="timeLeft.days">00</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Days</div>
                </div>
                <div class="text-2xl text-gray-400">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="timeLeft.hours">00</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Hours</div>
                </div>
                <div class="text-2xl text-gray-400">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="timeLeft.minutes">00</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Minutes</div>
                </div>
                <div class="text-2xl text-gray-400">:</div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="timeLeft.seconds">00</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Seconds</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Bids -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Live Bids</h3>
        <div class="space-y-3 max-h-64 overflow-y-auto" x-ref="bidsContainer">
            <template x-for="bid in recentBids" :key="bid.id">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium" x-text="bid.bidder.name.charAt(0)"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="bid.bidder.name"></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formatTime(bid.created_at)"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(bid.amount)"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="bid.is_winning">Winning</p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Active Participants -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Active Participants</h3>
        <div class="flex flex-wrap gap-2">
            <template x-for="user in activeUsers" :key="user.id">
                <div class="flex items-center space-x-2 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-medium" x-text="user.name.charAt(0)"></span>
                    </div>
                    <span class="text-sm font-medium" x-text="user.name"></span>
                    <span class="text-xs" x-text="user.role"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Bid Form -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
        <form @submit.prevent="placeBid" class="flex space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Bid</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" 
                           x-model="bidAmount" 
                           :min="minBidAmount"
                           step="0.01"
                           placeholder="Enter your bid"
                           class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           :disabled="isAuctionEnded || isPlacingBid">
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Minimum bid: <span x-text="formatCurrency(minBidAmount)"></span>
                </p>
            </div>
            <div class="flex items-end">
                <button type="submit" 
                        :disabled="isAuctionEnded || isPlacingBid || bidAmount < minBidAmount"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!isPlacingBid">Place Bid</span>
                    <span x-show="isPlacingBid" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Placing...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Status Messages -->
    <div x-show="statusMessage" 
         x-text="statusMessage"
         class="mt-4 p-3 rounded-lg"
         :class="statusType === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200'"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
    </div>
</div>

<script>
function auctionLive(domainId) {
    return {
        domainId: domainId,
        currentHighestBid: {{ $domain->bids()->where('is_winning', true)->first()?->amount ?? $domain->starting_price }},
        minBidAmount: {{ ($domain->bids()->where('is_winning', true)->first()?->amount ?? $domain->starting_price) + 1 }},
        recentBids: [],
        activeUsers: [],
        bidAmount: '',
        isPlacingBid: false,
        isAuctionEnded: false,
        timeLeft: { days: 0, hours: 0, minutes: 0, seconds: 0 },
        statusMessage: '',
        statusType: 'success',
        countdownInterval: null,

        init() {
            this.setupEchoListeners();
            this.loadRecentBids();
            this.startCountdown();
        },

        setupEchoListeners() {
            if (!window.Echo) {
                console.error('Echo not available');
                return;
            }

            // Listen for new bids
            window.Echo.private(`domain.${this.domainId}`)
                .listen('.bid.placed', (e) => {
                    this.recentBids.unshift(e);
                    this.currentHighestBid = e.current_highest;
                    this.minBidAmount = e.current_highest + 1;
                    this.scrollToTop();
                    this.showStatusMessage('New bid placed!', 'success');
                });

            // Listen for countdown updates
            window.Echo.private(`domain.${this.domainId}`)
                .listen('.auction.countdown', (e) => {
                    this.updateCountdown(e.seconds_left);
                    
                    if (e.is_ended) {
                        this.isAuctionEnded = true;
                        this.showStatusMessage('Auction has ended!', 'info');
                        this.stopCountdown();
                    } else if (e.is_ending) {
                        this.showStatusMessage('Auction ending soon!', 'warning');
                    }
                });

            // Join presence channel for active users
            window.Echo.join(`auction.${this.domainId}`)
                .here((users) => {
                    this.activeUsers = users;
                })
                .joining((user) => {
                    this.activeUsers.push(user);
                })
                .leaving((user) => {
                    this.activeUsers = this.activeUsers.filter(u => u.id !== user.id);
                });
        },

        async loadRecentBids() {
            try {
                const response = await fetch(`/api/domains/${this.domainId}/bids?limit=10`);
                const data = await response.json();
                this.recentBids = data.bids || [];
            } catch (error) {
                console.error('Failed to load recent bids:', error);
            }
        },

        async placeBid() {
            if (this.isPlacingBid || this.bidAmount < this.minBidAmount) return;

            this.isPlacingBid = true;
            this.statusMessage = '';

            try {
                const response = await fetch(`/api/domains/${this.domainId}/bids`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`,
                    },
                    body: JSON.stringify({
                        amount: parseFloat(this.bidAmount),
                    }),
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.message || 'Failed to place bid');
                }

                const data = await response.json();
                this.bidAmount = '';
                this.showStatusMessage('Bid placed successfully!', 'success');
            } catch (error) {
                this.showStatusMessage(error.message, 'error');
            } finally {
                this.isPlacingBid = false;
            }
        },

        startCountdown() {
            const endTime = new Date('{{ $domain->auction_end }}').getTime();
            this.updateCountdown(Math.max(0, Math.floor((endTime - Date.now()) / 1000)));
            
            this.countdownInterval = setInterval(() => {
                const secondsLeft = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
                this.updateCountdown(secondsLeft);
                
                if (secondsLeft === 0) {
                    this.isAuctionEnded = true;
                    this.stopCountdown();
                }
            }, 1000);
        },

        stopCountdown() {
            if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
                this.countdownInterval = null;
            }
        },

        updateCountdown(secondsLeft) {
            this.timeLeft = {
                days: Math.floor(secondsLeft / 86400),
                hours: Math.floor((secondsLeft % 86400) / 3600),
                minutes: Math.floor((secondsLeft % 3600) / 60),
                seconds: secondsLeft % 60
            };
        },

        scrollToTop() {
            this.$nextTick(() => {
                const container = this.$refs.bidsContainer;
                container.scrollTop = 0;
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(amount);
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

        showStatusMessage(message, type = 'success') {
            this.statusMessage = message;
            this.statusType = type;
            
            setTimeout(() => {
                this.statusMessage = '';
            }, 5000);
        }
    }
}
</script>
