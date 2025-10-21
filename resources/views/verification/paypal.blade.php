@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">PayPal Email Setup</h1>
                    <p class="mt-2 text-gray-600">Add your PayPal email address to receive payments from domain sales. Your email will be automatically verified upon submission.</p>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <div>
                                <h4 class="text-red-800 font-medium">Please correct the following errors:</h4>
                                <ul class="mt-2 text-red-700 text-sm list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->isPayPalVerified())
                    <!-- Already Verified -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                            <div>
                                <h3 class="text-lg font-medium text-green-800">PayPal Email Verified!</h3>
                                <p class="text-green-700">Your PayPal email ({{ $user->paypal_email }}) has been added and verified successfully.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('verification.paypal.submit') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="paypal_email" class="block text-sm font-medium text-gray-700">
                                PayPal Email Address
                            </label>
                            <div class="mt-1">
                                <input type="email" 
                                       name="paypal_email" 
                                       id="paypal_email" 
                                       value="{{ old('paypal_email', $user->paypal_email) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('paypal_email') border-red-300 @enderror"
                                       placeholder="your-email@example.com"
                                       required>
                            </div>
                            @error('paypal_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                This should be the email address associated with your PayPal account.
                            </p>
                        </div>

                        <!-- Verification Status -->
                        @if($paypalVerification)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-yellow-500 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Verification Pending</h4>
                                        <p class="text-sm text-yellow-700">
                                            Your PayPal email verification is currently under review. 
                                            You will be notified once it's approved.
                                        </p>
                                        <p class="text-xs text-yellow-600 mt-1">
                                            Submitted: {{ $paypalVerification->created_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <a href="{{ route('verification.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Verification
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit for Verification
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Information Section -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        About PayPal Email Setup
                    </h3>
                    <div class="space-y-3 text-sm text-blue-700">
                        <p><strong>Why add your PayPal email?</strong> This ensures you can receive payments from domain sales securely and quickly.</p>
                        <p><strong>What happens next?</strong> Your PayPal email will be automatically verified and ready to use immediately.</p>
                        <p><strong>Security:</strong> We only use your PayPal email for payment processing and never share it with third parties.</p>
                        <p><strong>Need help?</strong> If you don't have a PayPal account, you can create one at <a href="https://paypal.com" target="_blank" class="underline">paypal.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
