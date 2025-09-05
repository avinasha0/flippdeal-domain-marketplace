@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">PayPal Account Connection</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Connect your PayPal account to receive payments from domain sales. Your account will be automatically verified upon successful connection.</p>
                </div>

                @if($user->isPayPalVerified())
                    <!-- Already Connected -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-green-800 dark:text-green-200">PayPal Account Connected!</h3>
                                <p class="text-green-700 dark:text-green-300">Your PayPal account ({{ $user->paypal_email }}) has been successfully connected and verified.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4">
                        <a href="{{ route('paypal.connect') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.543-.68c-.78-.8-2.13-1.4-3.993-1.4H8.89c-.524 0-.968.382-1.05.9L6.12 19.106h4.956l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c3.24 0 5.67-1.4 6.45-4.2.1-.4.15-.8.15-1.2 0-.4-.05-.8-.15-1.2z"/>
                            </svg>
                            Update Account
                        </a>
                        <form method="POST" action="{{ route('paypal.disconnect') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to disconnect your PayPal account?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Disconnect
                            </button>
                        </form>
                    </div>
                @else
                    <!-- PayPal OAuth Connection -->
                    <div class="text-center space-y-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-8">
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.543-.68c-.78-.8-2.13-1.4-3.993-1.4H8.89c-.524 0-.968.382-1.05.9L6.12 19.106h4.956l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c3.24 0 5.67-1.4 6.45-4.2.1-.4.15-.8.15-1.2 0-.4-.05-.8-.15-1.2z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-medium text-blue-900 dark:text-blue-100 mb-3">
                                Connect Your PayPal Account
                            </h3>
                            <p class="text-blue-700 dark:text-blue-300 mb-6 max-w-md mx-auto">
                                Click the button below to securely connect your PayPal account. You'll be redirected to PayPal's official login page where you can sign in with your credentials.
                            </p>
                            <a href="{{ route('paypal.connect') }}" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.543-.68c-.78-.8-2.13-1.4-3.993-1.4H8.89c-.524 0-.968.382-1.05.9L6.12 19.106h4.956l1.12-7.106c.082-.518.526-.9 1.05-.9h2.19c3.24 0 5.67-1.4 6.45-4.2.1-.4.15-.8.15-1.2 0-.4-.05-.8-.15-1.2z"/>
                                </svg>
                                Connect with PayPal
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="text-center">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <p><strong>Secure & Safe</strong><br>Official PayPal OAuth</p>
                            </div>
                            <div class="text-center">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <p><strong>Instant Verification</strong><br>No waiting required</p>
                            </div>
                            <div class="text-center">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p><strong>Automatic Setup</strong><br>Ready to receive payments</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Information Section -->
                <div class="mt-8 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        About PayPal Integration
                    </h3>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                        <p><strong>Why connect your PayPal account?</strong> This ensures you can receive payments from domain sales securely and quickly.</p>
                        <p><strong>How it works:</strong> You'll be redirected to PayPal's official website to sign in with your credentials. Once authenticated, your account will be automatically verified.</p>
                        <p><strong>Security:</strong> We use PayPal's official OAuth system. Your login credentials are never shared with us - only your verified email address.</p>
                        <p><strong>Need help?</strong> If you don't have a PayPal account, you can create one at <a href="https://www.paypal.com/in/home" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">paypal.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
