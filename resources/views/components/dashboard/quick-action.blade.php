@props(['title', 'description', 'icon', 'href', 'color' => 'blue'])

@php
$colorClasses = [
    'blue' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30',
    'green' => 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30',
    'yellow' => 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:hover:bg-yellow-900/30',
    'red' => 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30',
    'purple' => 'bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:hover:bg-purple-900/30',
    'indigo' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:hover:bg-indigo-900/30',
];
@endphp

<a href="{{ $href }}" class="block p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200 {{ $colorClasses[$color] }}">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                {!! $icon !!}
            </div>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-medium">{{ $title }}</h3>
            <p class="text-sm opacity-75">{{ $description }}</p>
        </div>
        <div class="ml-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    </div>
</a>
