@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold leading-7 text-gray-900 dark:text-white sm:text-4xl sm:truncate">
                        Edit Profile
                    </h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                        Update your profile information and preferences.
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Profile Information -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Profile Information</h3>
                </div>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- About Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">About You</h3>
                </div>
            </div>
            <div class="p-6">
                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="bio" :value="__('Bio')" />
                        <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" placeholder="Tell us about your experience with domains and websites...">{{ old('bio', $user->bio ?? '') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">{{ __('Save About') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile Verification -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Profile Verification</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                    Complete these steps to verify your profile and increase trust.
                </p>
                
                <div class="space-y-4">
                    <!-- Email Verification -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if ($user->hasVerifiedEmail())
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Email Verification</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
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
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-3 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                                    Resend
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- PayPal Email Verification -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if ($user->isPayPalVerified())
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">PayPal Email</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if ($user->isPayPalVerified())
                                        {{ $user->paypal_email }} (Verified)
                                    @else
                                        Add your PayPal email for payments
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($user->isPayPalVerified())
                            <div class="flex space-x-2">
                                <a href="{{ route('paypal.connect') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-3 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                                    Update
                                </a>
                                <form method="POST" action="{{ route('paypal.disconnect') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 font-medium px-3 py-1 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200" onclick="return confirm('Are you sure you want to disconnect your PayPal account?')">
                                        Disconnect
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('paypal.connect') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-3 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                                Connect PayPal
                            </a>
                        @endif
                    </div>

                    <!-- Phone Verification -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Phone Verification</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Add and verify your phone number</p>
                            </div>
                        </div>
                        <button class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-3 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                            Add Phone
                        </button>
                    </div>

                    <!-- ID Verification -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">ID Verification</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Verify your identity with official documents</p>
                            </div>
                        </div>
                        <button class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-3 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200">
                            Verify ID
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Update Password</h3>
                </div>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gradient-to-r from-red-500 to-red-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Delete Account</h3>
                </div>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
