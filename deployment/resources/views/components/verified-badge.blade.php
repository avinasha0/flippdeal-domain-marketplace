@props(['domain', 'showTooltip' => true, 'size' => 'md'])

@php
    $isVerified = $domain->domain_verified && $domain->status === 'active';
    $verification = $domain->verifications()->where('status', 'verified')->latest()->first();
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
    $textSizeClasses = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
    ];
@endphp

@if($isVerified)
    <div class="inline-flex items-center space-x-1 {{ $showTooltip ? 'group relative' : '' }}">
        <!-- Verified Badge -->
        <div class="flex items-center space-x-1">
            <svg class="{{ $sizeClasses[$size] }} text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="{{ $textSizeClasses[$size] }} font-medium text-green-600 dark:text-green-400">
                Verified
            </span>
        </div>

        @if($showTooltip)
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 whitespace-nowrap">
                <div class="text-center">
                    <div class="font-medium">Domain Verified</div>
                    @if($verification)
                        <div class="text-gray-300 mt-1">
                            Method: {{ ucwords(str_replace('_', ' ', $verification->method)) }}
                        </div>
                        <div class="text-gray-300">
                            Verified: {{ $verification->updated_at->format('M j, Y') }}
                        </div>
                    @endif
                </div>
                <!-- Tooltip arrow -->
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
            </div>
        @endif

        <!-- Help Link -->
        <a href="{{ route('help.domain-verification') }}" 
           target="_blank"
           class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors"
           title="Learn about domain verification">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
            </svg>
        </a>
    </div>
@else
    <!-- Unverified State -->
    <div class="inline-flex items-center space-x-1 {{ $showTooltip ? 'group relative' : '' }}">
        <div class="flex items-center space-x-1">
            <svg class="{{ $sizeClasses[$size] }} text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="{{ $textSizeClasses[$size] }} text-gray-500 dark:text-gray-400">
                Unverified
            </span>
        </div>

        @if($showTooltip)
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 whitespace-nowrap">
                <div class="text-center">
                    <div class="font-medium">Domain Not Verified</div>
                    <div class="text-gray-300 mt-1">
                        Verification required to publish
                    </div>
                </div>
                <!-- Tooltip arrow -->
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
            </div>
        @endif
    </div>
@endif
