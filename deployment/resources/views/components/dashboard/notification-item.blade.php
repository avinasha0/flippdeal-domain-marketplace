@props(['title', 'message', 'time', 'type' => 'info', 'unread' => false])

@php
$typeClasses = [
    'info' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800',
    'success' => 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800',
    'warning' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800',
    'error' => 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800',
];

$iconClasses = [
    'info' => 'text-blue-500 dark:text-blue-400',
    'success' => 'text-green-500 dark:text-green-400',
    'warning' => 'text-yellow-500 dark:text-yellow-400',
    'error' => 'text-red-500 dark:text-red-400',
];

$icons = [
    'info' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'success' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
    'warning' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
    'error' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
];
@endphp

<div class="p-4 border-l-4 {{ $typeClasses[$type] }} {{ $unread ? 'bg-opacity-100' : 'bg-opacity-50' }}">
    <div class="flex">
        <div class="flex-shrink-0">
            <div class="{{ $iconClasses[$type] }}">
                {!! $icons[$type] !!}
            </div>
        </div>
        <div class="ml-3 flex-1">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $message }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $time }}</p>
        </div>
        @if($unread)
            <div class="flex-shrink-0">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            </div>
        @endif
    </div>
</div>
