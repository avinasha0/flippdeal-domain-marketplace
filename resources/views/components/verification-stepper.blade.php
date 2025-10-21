@props(['domain', 'showActions' => true])

@php
    $verification = $domain->verifications()->latest()->first();
    $steps = [
        'add_txt' => [
            'title' => 'Add TXT Record',
            'description' => 'Add DNS TXT record to verify ownership',
            'status' => 'pending',
            'timestamp' => null,
        ],
        'verified' => [
            'title' => 'Verified',
            'description' => 'DNS verification completed',
            'status' => 'pending',
            'timestamp' => null,
        ],
        'admin_approve' => [
            'title' => 'Admin Approve',
            'description' => 'Admin review and approval',
            'status' => 'pending',
            'timestamp' => null,
        ],
        'publish' => [
            'title' => 'Publish',
            'description' => 'Domain published to marketplace',
            'status' => 'pending',
            'timestamp' => null,
        ],
    ];

    // Determine current step and status
    if ($verification) {
        if ($verification->status === 'verified') {
            $steps['add_txt']['status'] = 'completed';
            $steps['verified']['status'] = 'completed';
            $steps['verified']['timestamp'] = $verification->updated_at;
            
            if ($domain->status === 'active') {
                $steps['admin_approve']['status'] = 'completed';
                $steps['publish']['status'] = 'completed';
                $steps['publish']['timestamp'] = $domain->published_at ?? $domain->updated_at;
            } else {
                $steps['admin_approve']['status'] = 'current';
            }
        } elseif ($verification->status === 'needs_admin') {
            $steps['add_txt']['status'] = 'completed';
            $steps['verified']['status'] = 'completed';
            $steps['admin_approve']['status'] = 'current';
        } else {
            $steps['add_txt']['status'] = 'current';
        }
    } else {
        $steps['add_txt']['status'] = 'current';
    }
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6" x-data="verificationStepper()">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Verification Progress</h3>
        @if($verification && $verification->canRetry())
            <button @click="retryVerification({{ $domain->id }})" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                Retry Verification
            </button>
        @endif
    </div>

    <div class="space-y-4">
        @foreach($steps as $key => $step)
            <div class="flex items-start space-x-4">
                <!-- Step Icon -->
                <div class="flex-shrink-0">
                    @if($step['status'] === 'completed')
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @elseif($step['status'] === 'current')
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        </div>
                    @else
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $loop->iteration }}</span>
                        </div>
                    @endif
                </div>

                <!-- Step Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium {{ $step['status'] === 'current' ? 'text-blue-600 dark:text-blue-400' : ($step['status'] === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400') }}">
                            {{ $step['title'] }}
                        </h4>
                        @if($step['timestamp'])
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($step['timestamp'])->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $step['description'] }}</p>

                    <!-- Step-specific actions -->
                    @if($key === 'add_txt' && $step['status'] === 'current' && $verification && $verification->method === 'dns_txt')
                        <div class="mt-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900 dark:text-blue-100">DNS TXT Record</span>
                                <button @click="copyToClipboard('{{ $verification->token }}')" 
                                        class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    Copy Token
                                </button>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded border font-mono text-sm break-all">
                                {{ $verification->token }}
                            </div>
                            <div class="mt-2 text-xs text-blue-700 dark:text-blue-300">
                                <strong>Instructions:</strong> Add this TXT record to your domain's DNS settings. TTL: 300 seconds (5 minutes)
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('help.dns-txt') }}" target="_blank" 
                                   class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    📖 How to add DNS TXT record →
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($key === 'admin_approve' && $step['status'] === 'current' && $verification && $verification->status === 'needs_admin')
                        <div class="mt-3 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-orange-900 dark:text-orange-100">Awaiting Admin Review</span>
                            </div>
                            <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                Your verification needs manual review. This usually takes 1-2 business days.
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('support.contact', ['subject' => 'Verification Review', 'domain' => $domain->full_domain]) }}" 
                                   class="text-xs text-orange-600 hover:text-orange-800 dark:text-orange-400">
                                    📞 Contact Support →
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($key === 'publish' && $step['status'] === 'current' && $domain->status !== 'active')
                        <div class="mt-3">
                            @if($domain->domain_verified)
                                <button @click="publishDomain({{ $domain->id }})" 
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Publish Domain
                                </button>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    Complete verification to publish
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function verificationStepper() {
    return {
        async retryVerification(domainId) {
            try {
                const response = await fetch(`/api/domains/${domainId}/verification/retry`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                if (response.ok) {
                    // Reload page to show updated status
                    window.location.reload();
                } else {
                    alert('Failed to retry verification. Please try again.');
                }
            } catch (error) {
                console.error('Error retrying verification:', error);
                alert('An error occurred. Please try again.');
            }
        },

        async publishDomain(domainId) {
            if (!confirm('Are you sure you want to publish this domain?')) {
                return;
            }

            try {
                const response = await fetch(`/api/domains/${domainId}/publish`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to publish domain. Please try again.');
                }
            } catch (error) {
                console.error('Error publishing domain:', error);
                alert('An error occurred. Please try again.');
            }
        },

        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                // Show success feedback
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('text-green-600');
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('text-green-600');
                }, 2000);
            } catch (error) {
                console.error('Failed to copy:', error);
                alert('Failed to copy to clipboard');
            }
        }
    }
}
</script>
