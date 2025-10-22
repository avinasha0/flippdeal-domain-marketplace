<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

        <!-- SEO Meta Tags -->
        <title>{{ config('app.name', 'FlippDeal') }} - @yield('title', 'Domain Marketplace')</title>
        <meta name="description" content="@yield('description', 'Buy and sell domains and websites for profit. The premier marketplace for domain flipping with secure escrow, instant transfers, and verified listings.')">
        <meta name="keywords" content="@yield('keywords', 'domain marketplace, buy domains, sell domains, domain flipping, website marketplace, domain investment, premium domains, domain auction, escrow service')">
        <meta name="author" content="FlippDeal">
        <meta name="robots" content="index, follow">
        <meta name="language" content="en">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="{{ config('app.name', 'FlippDeal') }} - @yield('title', 'Domain Marketplace')">
        <meta property="og:description" content="@yield('description', 'Buy and sell domains and websites for profit. The premier marketplace for domain flipping with secure escrow, instant transfers, and verified listings.')">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="FlippDeal">
        <meta property="og:image" content="{{ asset('favicon-512x512.svg') }}">
        <meta property="og:locale" content="en_US">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name', 'FlippDeal') }} - @yield('title', 'Domain Marketplace')">
        <meta name="twitter:description" content="@yield('description', 'Buy and sell domains and websites for profit. The premier marketplace for domain flipping with secure escrow, instant transfers, and verified listings.')">
        <meta name="twitter:image" content="{{ asset('favicon-512x512.svg') }}">
        
        <!-- Favicon and App Icons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('favicon-16x16.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset('favicon-32x32.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="192x192" href="{{ asset('favicon-192x192.svg') }}">
        <link rel="icon" type="image/svg+xml" sizes="512x512" href="{{ asset('favicon-512x512.svg') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        
        <!-- Theme Colors -->
        <meta name="theme-color" content="#3B82F6">
        <meta name="msapplication-TileColor" content="#3B82F6">
        <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- Activity Manager -->
        <script src="{{ asset('js/activity-manager.js') }}"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col">
            @include('components.header')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('components.footer')
        </div>
    </body>
</html>
