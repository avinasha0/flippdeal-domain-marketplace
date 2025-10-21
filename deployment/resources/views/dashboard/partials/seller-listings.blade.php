@if($domains->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($domains as $domain)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $domain->full_domain }}
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Active
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Price</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $domain->formatted_price }}
                        </span>
                    </div>
                    
                    @if($domain->enable_bidding)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Current Bid</span>
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format($domain->current_bid ?? 0, 2) }}
                            </span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Views</span>
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ $domain->view_count ?? 0 }}
                        </span>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('domains.show', $domain) }}" 
                       class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        View
                    </a>
                    <a href="{{ route('domains.edit', $domain) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        Edit
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No active listings</h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Start by creating your first domain listing.
        </p>
        <div class="mt-6">
            <a href="{{ route('domains.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                Create Listing
            </a>
        </div>
    </div>
@endif
