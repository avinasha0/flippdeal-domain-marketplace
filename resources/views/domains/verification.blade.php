@extends('layouts.app')

@section('head')
<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.notification-message {
    animation: slideIn 0.3s ease-out;
}
</style>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Domain Verification</h1>
                    <p class="mt-2 text-gray-600">Verify ownership of <strong>{{ $domain->full_domain }}</strong> to publish your listing.</p>
                </div>

                @if($domain->isVerified())
                    <!-- Already Verified -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-medium text-green-800">Domain Verified!</h3>
                                    <p class="text-green-700">Your domain ownership has been verified. You can now publish your listing.</p>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('my.domains.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    Continue to Domains
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Verification Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                            <h3 class="text-lg font-medium text-blue-800">Verification Required</h3>
                        </div>
                        <p class="text-blue-700 mb-4">To verify ownership of your domain, you can use file upload (if your domain has an active website) or DNS record methods. Choose one of the methods below:</p>
                        
                        @if(empty($instructions))
                            <div class="text-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-500 mb-4">Generating verification record...</p>
                                <p class="text-sm text-gray-400">Please wait while we set up your domain verification.</p>
                                <script>
                                    // Auto-refresh after 2 seconds if no instructions
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                </script>
                            </div>
                        @else
                            <!-- Website Status Check -->
                            @if(isset($instructions['website_status']))
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                    <h4 class="font-medium text-gray-900 mb-3">
                                        <i class="fas fa-globe text-blue-500 mr-2"></i>
                                        Website Status
                                    </h4>
                                    @if($instructions['website_status']['has_website'])
                                        <div class="flex items-center text-green-600 mb-2">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <span>Active website detected at: <strong>{{ $instructions['website_status']['url'] }}</strong></span>
                                        </div>
                                        <p class="text-sm text-gray-600">File-based verification is available for this domain.</p>
                                    @else
                                        <div class="flex items-center text-orange-600 mb-2">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>No active website detected</span>
                                        </div>
                                        <p class="text-sm text-gray-600">Only DNS-based verification methods are available.</p>
                                    @endif
                                </div>
                            @endif

                            <!-- File Verification Method (if website is active) -->
                            @if(isset($instructions['file_verification']) && $instructions['website_status']['has_website'])
                                <div class="bg-white border border-green-200 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-gray-900 mb-3">
                                        <i class="fas fa-upload text-green-500 mr-2"></i>
                                        Method 1: File Upload (Recommended)
                                    </h4>
                                    <div class="bg-green-50 p-3 rounded border mb-3">
                                        <p class="text-sm text-green-700 mb-2">
                                            <strong>Step 1:</strong> Download the verification file below
                                        </p>
                                        <a href="{{ route('domains.verification.download-file', $domain) }}" 
                                           class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            <i class="fas fa-download mr-2"></i>
                                            Download Verification File
                                        </a>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded border mb-3">
                                        <p class="text-sm text-gray-700 mb-2">
                                            <strong>Step 2:</strong> Upload the file to your website
                                        </p>
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-700">File name:</p>
                                            <code class="bg-white px-2 py-1 rounded border">{{ $instructions['file_verification']['filename'] }}</code>
                                        </div>
                                        <div class="text-sm mt-2">
                                            <p class="font-medium text-gray-700">Upload to:</p>
                                            <code class="bg-white px-2 py-1 rounded border">{{ $instructions['file_verification']['url'] }}</code>
                                        </div>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded border">
                                        <p class="text-sm text-blue-700 mb-2">
                                            <strong>Step 3:</strong> Verify the file is uploaded correctly
                                        </p>
                                        <button onclick="verifyByFile()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                            <i class="fas fa-check mr-2"></i>
                                            Verify File Upload
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">{{ $instructions['file_verification']['instructions'] }}</p>
                                </div>
                            @endif
                            <!-- TXT Record Method -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-gray-900 mb-3">
                                    <i class="fas fa-file-alt text-blue-500 mr-2"></i>
                                    @if(isset($instructions['file_verification']) && $instructions['website_status']['has_website'])
                                        Method 2: TXT Record
                                    @else
                                        Method 1: TXT Record
                                    @endif
                                </h4>
                                <div class="bg-gray-50 p-3 rounded border">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Type:</span>
                                            <span class="ml-2 font-mono bg-white px-2 py-1 rounded border">{{ $instructions['txt_record']['type'] }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Name:</span>
                                            <span class="ml-2 font-mono bg-white px-2 py-1 rounded border">{{ $instructions['txt_record']['name'] }}</span>
                                        </div>
                                        <div class="md:col-span-2">
                                            <span class="font-medium text-gray-700">Value:</span>
                                            <div class="mt-1 font-mono bg-white px-2 py-1 rounded border break-all">{{ $instructions['txt_record']['value'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $instructions['txt_record']['instructions'] }}</p>
                            </div>

                            <!-- CNAME Record Method -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-gray-900 mb-3">
                                    <i class="fas fa-link text-green-500 mr-2"></i>
                                    @if(isset($instructions['file_verification']) && $instructions['website_status']['has_website'])
                                        Method 3: CNAME Record
                                    @else
                                        Method 2: CNAME Record
                                    @endif
                                </h4>
                                <div class="bg-gray-50 p-3 rounded border">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Type:</span>
                                            <span class="ml-2 font-mono bg-white px-2 py-1 rounded border">{{ $instructions['cname_record']['type'] }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Name:</span>
                                            <span class="ml-2 font-mono bg-white px-2 py-1 rounded border">{{ $instructions['cname_record']['name'] }}</span>
                                        </div>
                                        <div class="md:col-span-2">
                                            <span class="font-medium text-gray-700">Value:</span>
                                            <div class="mt-1 font-mono bg-white px-2 py-1 rounded border">{{ $instructions['cname_record']['value'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ $instructions['cname_record']['instructions'] }}</p>
                            </div>

                            <!-- Verification Code -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                <h4 class="font-medium text-yellow-800 mb-2">
                                    <i class="fas fa-key text-yellow-600 mr-2"></i>
                                    Verification Code
                                </h4>
                                <p class="text-yellow-700 text-sm mb-2">Your unique verification code:</p>
                                <div class="font-mono bg-white px-3 py-2 rounded border text-lg font-bold text-gray-900">{{ $instructions['verification_code'] }}</div>
                            </div>

                            <!-- Expiry Warning -->
                            @if($instructions['expires_at'])
                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-orange-500 mr-3"></i>
                                        <div>
                                            <h4 class="font-medium text-orange-800">Verification Expires</h4>
                                            <p class="text-orange-700 text-sm">This verification record expires on {{ $instructions['expires_at']->format('M j, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if(!empty($instructions))
                            <button onclick="verifyDomain()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Verify Domain
                            </button>
                            
                            <button onclick="regenerateVerification()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded flex items-center justify-center">
                                <i class="fas fa-refresh mr-2"></i>
                                Regenerate Record
                            </button>
                            
                            <!-- Test Notification Button -->
                            <button onclick="testNotification()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded flex items-center justify-center">
                                <i class="fas fa-bell mr-2"></i>
                                Test Notification
                            </button>
                        @endif
                        
                        <a href="{{ route('domains.show', $domain) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Domain
                        </a>
                    </div>
                @endif

                <!-- Help Section -->
                <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-question-circle text-gray-500 mr-2"></i>
                        Need Help?
                    </h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <p><strong>File Upload Method:</strong> If your domain has an active website, you can download a verification file and upload it to your website's root directory (public_html, www, or htdocs folder). This is the fastest verification method.</p>
                        <p><strong>What is DNS?</strong> DNS (Domain Name System) is like a phone book for the internet. It translates domain names into IP addresses.</p>
                        <p><strong>Where do I add DNS records?</strong> You need to add these records in your domain registrar's DNS management panel or your hosting provider's DNS settings.</p>
                        <p><strong>How long does verification take?</strong> File upload verification is instant. DNS changes can take anywhere from a few minutes to 48 hours to propagate worldwide.</p>
                        <p><strong>Can't find DNS settings?</strong> Contact your domain registrar or hosting provider for assistance with DNS management.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Processing...</h3>
            <p class="text-sm text-gray-500 mt-2" id="loadingMessage">Please wait while we process your request.</p>
        </div>
    </div>
</div>

<script>
function showLoading(message = 'Please wait while we process your request.') {
    document.getElementById('loadingMessage').textContent = message;
    document.getElementById('loadingModal').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingModal').classList.add('hidden');
}

function showMessage(message, type = 'success') {
    console.log('showMessage called:', message, type);
    
    // Remove any existing notifications first
    const existingNotifications = document.querySelectorAll('.notification-message');
    existingNotifications.forEach(notification => notification.remove());
    
    const alertClass = type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white';
    const messageDiv = document.createElement('div');
    messageDiv.className = `notification-message fixed top-4 right-4 ${alertClass} px-6 py-4 rounded-lg shadow-lg z-50 max-w-md`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
    `;
    
    messageDiv.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${type === 'success' ? 
                    '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                    '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                }
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 focus:outline-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(messageDiv);
    
    // Auto-remove after 8 seconds
    setTimeout(() => {
        if (messageDiv.parentElement) {
            messageDiv.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 300);
        }
    }, 8000);
    
    console.log('Notification added to DOM');
}

async function generateVerification() {
    showLoading('Generating verification record...');
    
    try {
        const response = await fetch(`{{ route('domains.verification.generate', $domain) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

async function verifyDomain() {
    console.log('verifyDomain function called');
    showLoading('Verifying domain ownership...');
    
    try {
        console.log('Making request to:', '{{ route('domains.verification.verify', $domain) }}');
        const response = await fetch(`{{ route('domains.verification.verify', $domain) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("my.domains.index") }}';
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error in verifyDomain:', error);
        showMessage('An error occurred. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

async function regenerateVerification() {
    if (!confirm('Are you sure you want to regenerate the verification record? The current record will be invalidated.')) {
        return;
    }
    
    showLoading('Regenerating verification record...');
    
    try {
        const response = await fetch(`{{ route('domains.verification.regenerate', $domain) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        showMessage('An error occurred. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

async function verifyByFile() {
    console.log('verifyByFile function called');
    showLoading('Verifying file upload...');
    
    try {
        console.log('Making request to:', '{{ route('domains.verification.verify-file', $domain) }}');
        const response = await fetch(`{{ route('domains.verification.verify-file', $domain) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => {
                window.location.href = '{{ route("my.domains.index") }}';
            }, 1500);
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error in verifyByFile:', error);
        showMessage('An error occurred. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

function testNotification() {
    console.log('Testing notification system...');
    showMessage('This is a test notification! If you can see this, notifications are working.', 'success');
    
    setTimeout(() => {
        showMessage('This is a test error notification!', 'error');
    }, 2000);
}
</script>
@endsection
