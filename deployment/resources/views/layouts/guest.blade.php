<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        <title>{{ config('app.name', 'FlippDeal') }} - @yield('title', 'Domain Marketplace')</title>
        <meta name="description" content="@yield('description', 'Buy and sell domains and websites for profit. The premier marketplace for domain flipping with secure escrow, instant transfers, and verified listings.')">
        <meta name="keywords" content="@yield('keywords', 'domain marketplace, buy domains, sell domains, domain flipping, website marketplace, domain investment, premium domains, domain auction, escrow service')">
        <meta name="author" content="FlippDeal">
        <meta name="robots" content="index, follow">
        
        <!-- Favicon and App Icons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('favicon-16x16.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset('favicon-32x32.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
        
        <!-- Theme Colors -->
        <meta name="theme-color" content="#3B82F6">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('components.header')
            
            <div class="flex-1 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                <!-- Auth Card -->
                <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            @include('components.footer')
    </body>
</html>
