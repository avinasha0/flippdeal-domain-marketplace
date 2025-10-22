@props(['notification'])

<div class="flex items-start space-x-3 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
    <div class="flex-shrink-0">
        @if($notification->data['type_label'] === 'credit')
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
        @else
            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
            </div>
        @endif
    </div>
    
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                Wallet {{ $notification->data['action'] }}: {{ $notification->data['formatted_amount'] }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ \Carbon\Carbon::parse($notification->data['created_at'])->diffForHumans() }}
            </p>
        </div>
        
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $notification->data['description'] }}
        </p>
        
        <div class="flex items-center mt-2 space-x-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                @if($notification->data['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($notification->data['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                @elseif($notification->data['status'] === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                @endif">
                {{ ucfirst($notification->data['status']) }}
            </span>
            
            @if($notification->data['type_label'] === 'credit')
                <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                    +{{ $notification->data['formatted_amount'] }}
                </span>
            @else
                <span class="text-xs text-red-600 dark:text-red-400 font-medium">
                    -{{ $notification->data['formatted_amount'] }}
                </span>
            @endif
        </div>
    </div>
</div>
