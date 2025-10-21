@props(['disabled' => false, 'error' => false, 'size' => 'md'])

@php
$baseClasses = 'block w-full rounded-lg border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 dark:disabled:bg-gray-800';

$sizes = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-4 py-3 text-base',
];

$stateClasses = $error 
    ? 'border-red-300 dark:border-red-600 focus:border-red-500 focus:ring-red-500 bg-red-50 dark:bg-red-900/20' 
    : 'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-800';

$classes = $baseClasses . ' ' . $sizes[$size] . ' ' . $stateClasses;
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => $classes]) }}>
