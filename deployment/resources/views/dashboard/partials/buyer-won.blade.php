@if($bids->count() > 0)
    <div class="space-y-6">
        @foreach($bids as $bid)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $bid->domain->full_domain }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Won on {{ $bid->created_at->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Won
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Winning Bid</span>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">
                            {{ $bid->formatted_amount }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Seller</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $bid->domain->user->name }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Domain Price</span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $bid->domain->formatted_price }}
                        </p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('domains.show', $bid->domain) }}" 
                       class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        View Domain
                    </a>
                    <button class="inline-flex items-center px-4 py-2 border border-green-300 dark:border-green-600 text-sm font-medium rounded-lg text-green-700 dark:text-green-300 bg-white dark:bg-gray-600 hover:bg-green-50 dark:hover:bg-green-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Complete Purchase
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $bids->links() }}
    </div>
@else
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No won domains yet</h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Start bidding on domains to win auctions and purchase domains.
        </p>
        <div class="mt-6">
            <a href="{{ route('domains.public.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                Browse Domains
            </a>
        </div>
    </div>
@endif
