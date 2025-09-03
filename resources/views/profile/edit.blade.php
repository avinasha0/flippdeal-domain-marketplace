<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FlippDeal') }} - Profile</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white">
            <!-- Custom Header -->
            <header class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Logo and App Name -->
                        <div class="flex items-center space-x-8">
                            <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-80 transition-opacity duration-200">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">F</span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h1 class="text-xl font-bold text-gray-900">FlippDeal</h1>
                                </div>
                            </a>

                            <!-- Explore Dropdown -->
                            <div class="relative" x-data="{ exploreOpen: false }">
                                <button @click="exploreOpen = !exploreOpen" class="flex items-center text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    Explore
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Explore Dropdown Menu -->
                                <div x-show="exploreOpen" @click.away="exploreOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                            </svg>
                                            Domains
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Websites
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="flex items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="ml-2 text-gray-700 font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 h-4 w-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Profile Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Profile Settings
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                        <!-- Profile Information -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <!-- About Section -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('About') }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('Tell us more about yourself and your flipping business.') }}
                                        </p>
                                    </header>

                                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                        @csrf
                                        @method('patch')

                                        <div>
                                            <x-input-label for="bio" :value="__('Bio')" />
                                            <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tell us about your experience with domains and websites...">{{ old('bio', $user->bio ?? '') }}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                                        </div>

                                        <div>
                                            <x-input-label for="location" :value="__('Location')" />
                                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $user->location ?? '')" placeholder="City, Country" />
                                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                                        </div>

                                        <div>
                                            <x-input-label for="website" :value="__('Website')" />
                                            <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" :value="old('website', $user->website ?? '')" placeholder="https://yourwebsite.com" />
                                            <x-input-error class="mt-2" :messages="$errors->get('website')" />
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <x-primary-button>{{ __('Save About') }}</x-primary-button>
                                        </div>
                                    </form>
                                </section>
                            </div>
                        </div>

                        <!-- Profile Verification -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium text-gray-900">
                                            {{ __('Profile Verification') }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('Complete these steps to verify your profile and increase trust.') }}
                                        </p>
                                    </header>

                                    <div class="mt-6 space-y-4">
                                        <!-- Email Verification -->
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if ($user->hasVerifiedEmail())
                                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900">Email Verification</h3>
                                                    <p class="text-sm text-gray-500">
                                                        @if ($user->hasVerifiedEmail())
                                                            Your email is verified
                                                        @else
                                                            Verify your email address
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            @if (!$user->hasVerifiedEmail())
                                                <form method="post" action="{{ route('verification.send') }}">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                                        Resend
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- PayPal Email Verification -->
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if (isset($user->paypal_email) && $user->paypal_email)
                                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900">PayPal Email</h3>
                                                    <p class="text-sm text-gray-500">
                                                        @if (isset($user->paypal_email) && $user->paypal_email)
                                                            {{ $user->paypal_email }}
                                                        @else
                                                            Add your PayPal email for payments
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                                @if (isset($user->paypal_email) && $user->paypal_email)
                                                    Change
                                                @else
                                                    Add PayPal
                                                @endif
                                            </button>
                                        </div>

                                        <!-- Phone Verification -->
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900">Phone Verification</h3>
                                                    <p class="text-sm text-gray-500">Add and verify your phone number</p>
                                                </div>
                                            </div>
                                            <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                                Add Phone
                                            </button>
                                        </div>

                                        <!-- ID Verification -->
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900">ID Verification</h3>
                                                    <p class="text-sm text-gray-500">Verify your identity with official documents</p>
                                                </div>
                                            </div>
                                            <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                                Verify ID
                                            </button>
                                        </div>

                                        <!-- Social Media Verification -->
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <h3 class="text-sm font-medium text-gray-900">Social Media</h3>
                                                    <p class="text-sm text-gray-500">Connect your social media accounts</p>
                                                </div>
                                            </div>
                                            <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                                Connect
                                            </button>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <!-- Update Password -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
