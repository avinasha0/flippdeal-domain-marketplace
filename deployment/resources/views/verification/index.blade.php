@extends('layouts.app')

@section('title', 'Profile Verification')
@section('description', 'Complete your FlippDeal profile verification to unlock all marketplace features, increase trust, and access premium selling tools.')
@section('keywords', 'profile verification, account verification, identity verification, marketplace verification, trust badge, verified seller')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Profile Verification</h1>
                    <p class="mt-2 text-gray-600">Complete your profile verification to access all marketplace features.</p>
                </div>

                <!-- Verification Status Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- PayPal Email Verification -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">PayPal Email</h3>
                            @if($user->isPayPalVerified())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Verify your PayPal email address to receive payments from domain sales.
                        </p>
                        @if($user->paypal_email)
                            <p class="text-sm font-medium text-gray-900 mb-2">Email: {{ $user->paypal_email }}</p>
                        @endif
                        <a href="{{ route('verification.paypal') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            @if($user->isPayPalVerified())
                                <i class="fas fa-edit mr-2"></i>
                                Update
                            @else
                                <i class="fas fa-plus mr-2"></i>
                                Verify
                            @endif
                        </a>
                    </div>

                    <!-- Government ID Verification -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Government ID</h3>
                            @if($user->isGovernmentIdVerified())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-4">
                            Upload a government-issued ID to verify your identity and become a trusted seller.
                        </p>
                        <a href="{{ route('verification.government-id') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            @if($user->isGovernmentIdVerified())
                                <i class="fas fa-edit mr-2"></i>
                                Update
                            @else
                                <i class="fas fa-upload mr-2"></i>
                                Upload
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Overall Status -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Verification Status</h3>
                            <p class="text-sm text-gray-600">
                                @if($user->isFullyVerified())
                                    Your profile is fully verified! You can now access all marketplace features.
                                @else
                                    Complete the verification steps above to unlock all marketplace features.
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            @if($user->isFullyVerified())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Fully Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Verification Required
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Verification History -->
                @if($verifications->count() > 0)
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Verification History</h3>
                        <div class="space-y-4">
                            @foreach($verifications as $verification)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($verification->type === 'paypal_email')
                                                <i class="fas fa-paypal text-blue-500 text-xl"></i>
                                            @elseif($verification->type === 'government_id')
                                                <i class="fas fa-id-card text-green-500 text-xl"></i>
                                            @else
                                                <i class="fas fa-check-circle text-gray-500 text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ ucfirst(str_replace('_', ' ', $verification->type)) }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Submitted {{ $verification->created_at->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        @if($verification->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Approved
                                            </span>
                                        @elseif($verification->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Help Section -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">
                        <i class="fas fa-question-circle mr-2"></i>
                        Need Help?
                    </h3>
                    <div class="space-y-3 text-sm text-blue-700">
                        <p><strong>Why do I need verification?</strong> Verification helps build trust in our marketplace and ensures secure transactions.</p>
                        <p><strong>How long does verification take?</strong> PayPal email verification is usually instant, while government ID verification takes 1-3 business days.</p>
                        <p><strong>Is my information secure?</strong> Yes, we use industry-standard encryption and never share your personal information.</p>
                        <p><strong>Need assistance?</strong> Contact our support team if you have any questions about the verification process.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
