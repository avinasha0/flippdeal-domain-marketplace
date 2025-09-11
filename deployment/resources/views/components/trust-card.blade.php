@props(['user', 'showFullCard' => false])

@php
    $rating = $user->seller_rating_avg ?? 0;
    $ratingCount = $user->seller_rating_count ?? 0;
    $totalSales = $user->total_sales_count ?? 0;
    $avgResponseTime = $user->avg_response_time_hours ?? null;
    $isVerified = $user->isFullyVerified();
    $joinedDate = $user->created_at;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 {{ $showFullCard ? 'p-6' : '' }}">
    @if($showFullCard)
        <div class="flex items-center space-x-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                <span class="text-white text-lg font-semibold">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Seller</p>
            </div>
        </div>
    @endif

    <div class="space-y-3">
        <!-- Verification Status -->
        <div class="flex items-center space-x-2">
            @if($isVerified)
                <div class="flex items-center space-x-1">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium text-green-600 dark:text-green-400">Verified Seller</span>
                </div>
            @else
                <div class="flex items-center space-x-1">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Unverified</span>
                </div>
            @endif
        </div>

        <!-- Rating -->
        @if($rating > 0)
            <div class="flex items-center space-x-2">
                <div class="flex items-center space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating)
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @elseif($i - 0.5 <= $rating)
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="half-{{ $i }}">
                                        <stop offset="50%" stop-color="currentColor"/>
                                        <stop offset="50%" stop-color="#E5E7EB"/>
                                    </linearGradient>
                                </defs>
                                <path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ number_format($rating, 1) }} ({{ $ratingCount }} {{ $ratingCount === 1 ? 'review' : 'reviews' }})
                </span>
            </div>
        @endif

        <!-- Sales Count -->
        @if($totalSales > 0)
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $totalSales }} {{ $totalSales === 1 ? 'sale' : 'sales' }}
                </span>
            </div>
        @endif

        <!-- Response Time -->
        @if($avgResponseTime)
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Avg response: {{ $avgResponseTime }}h
                </span>
            </div>
        @endif

        <!-- Member Since -->
        <div class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Member since {{ $joinedDate->format('M Y') }}
            </span>
        </div>

        @if($showFullCard)
            <!-- Action Buttons -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-2">
                    <button @click="viewSellerProfile({{ $user->id }})" 
                            class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        View Profile
                    </button>
                    <button @click="contactSeller({{ $user->id }})" 
                            class="px-3 py-1 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Contact
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@if($showFullCard)
<script>
function viewSellerProfile(userId) {
    window.open(`/users/${userId}/profile`, '_blank');
}

function contactSeller(userId) {
    // This would open a contact/message modal or redirect to messaging
    window.location.href = `/conversations/new?user_id=${userId}`;
}
</script>
@endif
