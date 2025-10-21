@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Government ID Verification</h1>
                    <p class="mt-2 text-gray-600">Upload a government-issued ID to verify your identity and become a trusted seller.</p>
                </div>

                @if($user->isGovernmentIdVerified())
                    <!-- Already Verified -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                            <div>
                                <h3 class="text-lg font-medium text-green-800">Government ID Verified!</h3>
                                <p class="text-green-700">Your government ID has been verified and approved. You are now a trusted seller.</p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Verification Form -->
                    <form method="POST" action="{{ route('verification.government-id.submit') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="government_id" class="block text-sm font-medium text-gray-700">
                                Government-Issued ID
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="government_id" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="government_id" 
                                                   name="government_id" 
                                                   type="file" 
                                                   accept=".jpg,.jpeg,.png,.pdf"
                                                   class="sr-only"
                                                   required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, PDF up to 5MB
                                    </p>
                                </div>
                            </div>
                            @error('government_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Verification Status -->
                        @if($governmentIdVerification)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-yellow-500 mr-3"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Verification Pending</h4>
                                        <p class="text-sm text-yellow-700">
                                            Your government ID verification is currently under review. 
                                            You will be notified once it's approved.
                                        </p>
                                        <p class="text-xs text-yellow-600 mt-1">
                                            Submitted: {{ $governmentIdVerification->created_at->format('M j, Y g:i A') }}
                                        </p>
                                        @if($governmentIdVerification->status === 'rejected' && $governmentIdVerification->rejection_reason)
                                            <p class="text-xs text-red-600 mt-2">
                                                <strong>Reason:</strong> {{ $governmentIdVerification->rejection_reason }}
                                            </p>
                                        @endif
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
                                <i class="fas fa-upload mr-2"></i>
                                Submit for Verification
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Information Section -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        About Government ID Verification
                    </h3>
                    <div class="space-y-3 text-sm text-blue-700">
                        <p><strong>Accepted IDs:</strong> Driver's license, passport, national ID card, or other government-issued photo ID.</p>
                        <p><strong>Requirements:</strong> Clear, readable image with all text visible. File must be in JPG, PNG, or PDF format.</p>
                        <p><strong>Processing time:</strong> Verification typically takes 1-3 business days.</p>
                        <p><strong>Security:</strong> Your ID is encrypted and stored securely. We never share your personal information.</p>
                        <p><strong>Benefits:</strong> Verified sellers receive priority in search results and can access advanced selling features.</p>
                    </div>
                </div>

                <!-- Privacy Notice -->
                <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Privacy & Security
                    </h4>
                    <p class="text-xs text-gray-600">
                        Your government ID is encrypted and stored securely. We use industry-standard security measures 
                        to protect your personal information and only use it for identity verification purposes. 
                        Your ID is never shared with third parties or used for any other purpose.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('government_id').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        if (fileSize > 5) {
            alert('File size must be less than 5MB');
            e.target.value = '';
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            alert('Please upload a JPG, PNG, or PDF file');
            e.target.value = '';
            return;
        }
    }
});
</script>
@endsection
