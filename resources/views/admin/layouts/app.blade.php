<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>{{ config('app.name', 'FlippDeal') }} - Admin Panel</title>
    <meta name="description" content="FlippDeal admin panel for managing domain marketplace, users, and system settings.">
    <meta name="keywords" content="admin panel, domain management, marketplace admin, system administration">
    <meta name="author" content="FlippDeal">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/svg+xml" sizes="16x16" href="{{ asset('favicon-16x16.svg') }}">
    <link rel="icon" type="image/svg+xml" sizes="32x32" href="{{ asset('favicon-32x32.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    
    <!-- Theme Colors -->
    <meta name="theme-color" content="#8B5CF6">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link href="{{ asset('build/assets/app-BANQcGNA.css') }}" rel="stylesheet">
<script src="{{ asset('build/assets/app-DtCVKgHt.js') }}"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        @include('components.header')
        
        <!-- Admin Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                <i class="fas fa-crown text-purple-600 mr-2"></i>
                                Admin Panel
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-users mr-2"></i>
                                Users
                            </a>
                            <a href="{{ route('admin.domains.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.domains.*') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-globe mr-2"></i>
                                Domains
                            </a>
                            <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.verifications.*') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-check-circle mr-2"></i>
                                Verifications
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.settings.*') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-cog mr-2"></i>
                                Settings
                            </a>
                            <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.audit-logs.*') ? 'border-purple-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                <i class="fas fa-history mr-2"></i>
                                Audit Logs
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-home mr-1"></i>
                                    Back to Site
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700">
                                        <i class="fas fa-sign-out-alt mr-1"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.users.*') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-users mr-2"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.domains.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.domains.*') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-globe mr-2"></i>
                        Domains
                    </a>
                    <a href="{{ route('admin.verifications.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.verifications.*') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-check-circle mr-2"></i>
                        Verifications
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.settings.*') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.audit-logs.*') ? 'border-purple-500 text-purple-700 bg-purple-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-history mr-2"></i>
                        Audit Logs
                    </a>
                </div>

                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:text-gray-800 focus:bg-gray-100 transition duration-150 ease-in-out">
                            <i class="fas fa-home mr-2"></i>
                            Back to Site
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:text-gray-800 focus:bg-gray-100 transition duration-150 ease-in-out">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        @include('components.footer')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminApp', () => ({
                open: false
            }))
        })
    </script>
</body>
</html>
