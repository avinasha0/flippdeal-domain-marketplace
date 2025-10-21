@props(['title', 'value', 'icon', 'trend' => null, 'trendValue' => null, 'color' => 'blue', 'href' => null])

@php
$colorClasses = [
    'blue' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
    'green' => 'bg-gradient-to-r from-green-500 to-emerald-600 text-white',
    'yellow' => 'bg-gradient-to-r from-yellow-500 to-orange-500 text-white',
    'red' => 'bg-gradient-to-r from-red-500 to-pink-600 text-white',
    'purple' => 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white',
    'indigo' => 'bg-gradient-to-r from-indigo-500 to-blue-600 text-white',
];
@endphp

@if($href)
    <a href="{{ $href }}" class="block group">
@endif
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 {{ $href ? 'group-hover:scale-105' : '' }}">
    <div class="p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 {{ $colorClasses[$color] }} rounded-xl flex items-center justify-center shadow-lg">
                    {!! $icon !!}
                </div>
            </div>
            <div class="ml-4 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $value }}
                        </div>
                        @if($trend && $trendValue)
                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $trend === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                @if($trend === 'up')
                                    <svg class="self-center flex-shrink-0 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="self-center flex-shrink-0 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="sr-only">{{ $trend === 'up' ? 'Increased' : 'Decreased' }} by</span>
                                {{ $trendValue }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@if($href)
    </a>
@endif
