@extends('layouts.app')

@section('title', 'Watchlist')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Watchlist</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Keep track of domains you're interested in</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $watchlist->total() }} domains
                    </div>
                </div>
            </div>
        </div>

        <!-- Watchlist Grid -->
        @if($watchlist->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($watchlist as $item)
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300">
                        <!-- Domain Header -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($item->domain->domain_name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $item->domain->full_domain }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            by {{ $item->domain->user->name }}
                                        </p>
                                    </div>
                                </div>
                                <button onclick="removeFromWatchlist({{ $item->domain->id }})" 
                                        class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Domain Details -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Price</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $item->domain->formatted_price }}
                                    </span>
                                </div>
                                
                                @if($item->domain->category)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Category</span>
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ ucfirst($item->domain->category) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Added</span>
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ $item->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                @if($item->domain->enable_bidding && $item->domain->current_bid)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Current Bid</span>
                                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                            ${{ number_format($item->domain->current_bid, 2) }}
                                        </span>
                                    </div>
                                @endif

                                @if($item->domain->enable_buy_now)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Buy Now</span>
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                            ${{ number_format($item->domain->buy_now_price, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Features -->
                        @if($item->domain->additional_features)
                            <div class="px-6 pb-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($item->domain->additional_features, true) as $feature)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="px-6 pb-6">
                            <div class="flex space-x-3">
                                <a href="{{ route('domains.show', $item->domain) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                    View Domain
                                </a>
                                <button onclick="contactSeller({{ $item->domain->id }})" 
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Contact
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $watchlist->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-12 text-center">
                <div class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-600">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No domains in your watchlist</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Start building your watchlist by adding domains you're interested in.
                </p>
                <div class="mt-6">
                    <a href="{{ route('domains.public.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        Browse Domains
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function removeFromWatchlist(domainId) {
    if (confirm('Are you sure you want to remove this domain from your watchlist?')) {
        fetch('/watchlist', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                domain_id: domainId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to remove domain from watchlist.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove domain from watchlist.');
        });
    }
}

function contactSeller(domainId) {
    // This would open a modal or redirect to contact form
    // For now, redirect to domain page
    window.location.href = `/domains/${domainId}`;
}
</script>
@endsection
