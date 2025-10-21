@props(['transaction', 'userRole' => 'buyer'])

@php
    $checklist = $userRole === 'seller' ? $transaction->seller_checklist : $transaction->buyer_checklist;
    $items = $userRole === 'seller' ? [
        'mark_ready' => [
            'title' => 'Mark domain as ready for transfer',
            'description' => 'Confirm domain is ready for transfer',
            'action' => 'checkbox',
        ],
        'provide_auth_code' => [
            'title' => 'Provide EPP/Auth code',
            'description' => 'Enter the authorization code from your registrar',
            'action' => 'form',
        ],
        'initiate_transfer' => [
            'title' => 'Initiate transfer at registrar',
            'description' => 'Start the transfer process with your registrar',
            'action' => 'link',
        ],
        'upload_evidence' => [
            'title' => 'Upload transfer evidence',
            'description' => 'Upload screenshot or transfer confirmation',
            'action' => 'upload',
        ],
        'confirm_whois_change' => [
            'title' => 'Confirm WHOIS/email change',
            'description' => 'Verify WHOIS data has been updated',
            'action' => 'confirm',
        ],
    ] : [
        'payment_completed' => [
            'title' => 'Payment completed',
            'description' => 'Payment has been processed and held in escrow',
            'action' => 'status',
        ],
        'confirm_payment' => [
            'title' => 'Confirm payment received',
            'description' => 'Verify payment is in escrow account',
            'action' => 'confirm',
        ],
        'confirm_transfer' => [
            'title' => 'Confirm transfer received',
            'description' => 'Verify domain transfer has been completed',
            'action' => 'confirm',
        ],
        'mark_complete' => [
            'title' => 'Mark transfer complete',
            'description' => 'Confirm transfer is complete to release escrow',
            'action' => 'button',
        ],
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6" x-data="transferChecklist({{ $transaction->id }}, '{{ $userRole }}')">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $userRole === 'seller' ? 'Seller' : 'Buyer' }} Checklist
        </h3>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span x-text="completedCount"></span> of <span x-text="totalCount"></span> completed
        </div>
    </div>

    <div class="space-y-4">
        @foreach($items as $key => $item)
            <div class="flex items-start space-x-4 p-4 rounded-lg border {{ $checklist[$key]['status'] ?? 'pending' === 'completed' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600' }}">
                <!-- Status Icon -->
                <div class="flex-shrink-0">
                    @if(($checklist[$key]['status'] ?? 'pending') === 'completed')
                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @elseif(($checklist[$key]['status'] ?? 'pending') === 'blocked')
                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-6 h-6 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $loop->iteration }}</span>
                        </div>
                    @endif
                </div>

                <!-- Item Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium {{ ($checklist[$key]['status'] ?? 'pending') === 'completed' ? 'text-green-600 dark:text-green-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $item['title'] }}
                        </h4>
                        @if(($checklist[$key]['timestamp'] ?? null))
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($checklist[$key]['timestamp'])->diffForHumans() }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $item['description'] }}</p>

                    <!-- Action Buttons -->
                    @if(($checklist[$key]['status'] ?? 'pending') !== 'completed')
                        <div class="mt-3">
                            @if($item['action'] === 'checkbox')
                                <button @click="markItem('{{ $key }}', 'completed')" 
                                        class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Mark as Done
                                </button>
                            @elseif($item['action'] === 'form')
                                <div class="space-y-2">
                                    <input type="text" 
                                           x-model="authCode" 
                                           placeholder="Enter EPP/Auth code"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                    <button @click="submitAuthCode('{{ $key }}')" 
                                            class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Submit Code
                                    </button>
                                </div>
                            @elseif($item['action'] === 'link')
                                <a href="{{ route('help.domain-transfer') }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    View Instructions
                                </a>
                            @elseif($item['action'] === 'upload')
                                <div class="space-y-2">
                                    <input type="file" 
                                           @change="handleFileUpload($event, '{{ $key }}')"
                                           accept="image/*,.pdf"
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <button @click="uploadEvidence('{{ $key }}')" 
                                            :disabled="!selectedFile"
                                            class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Upload Evidence
                                    </button>
                                </div>
                            @elseif($item['action'] === 'confirm')
                                <button @click="markItem('{{ $key }}', 'completed')" 
                                        class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Confirm
                                </button>
                            @elseif($item['action'] === 'button')
                                <button @click="markItem('{{ $key }}', 'completed')" 
                                        class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Mark Complete
                                </button>
                            @endif
                        </div>
                    @endif

                    <!-- Evidence Link -->
                    @if(($checklist[$key]['evidence_url'] ?? null))
                        <div class="mt-2">
                            <a href="{{ $checklist[$key]['evidence_url'] }}" target="_blank" 
                               class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                📎 View Evidence
                            </a>
                        </div>
                    @endif

                    <!-- Status Message -->
                    @if(($checklist[$key]['status'] ?? 'pending') === 'blocked')
                        <div class="mt-2 text-xs text-red-600 dark:text-red-400">
                            {{ $checklist[$key]['message'] ?? 'This step is currently blocked' }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function transferChecklist(transactionId, userRole) {
    return {
        authCode: '',
        selectedFile: null,
        completedCount: {{ collect($checklist)->where('status', 'completed')->count() }},
        totalCount: {{ count($items) }},

        async markItem(itemKey, status) {
            try {
                const response = await fetch(`/api/transactions/${transactionId}/checklist/mark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        item: itemKey,
                        status: status,
                        user_role: userRole
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to update checklist item. Please try again.');
                }
            } catch (error) {
                console.error('Error updating checklist:', error);
                alert('An error occurred. Please try again.');
            }
        },

        async submitAuthCode(itemKey) {
            if (!this.authCode.trim()) {
                alert('Please enter an auth code.');
                return;
            }

            try {
                const response = await fetch(`/api/transactions/${transactionId}/checklist/mark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        item: itemKey,
                        status: 'completed',
                        user_role: userRole,
                        data: { auth_code: this.authCode }
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to submit auth code. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting auth code:', error);
                alert('An error occurred. Please try again.');
            }
        },

        handleFileUpload(event, itemKey) {
            this.selectedFile = event.target.files[0];
        },

        async uploadEvidence(itemKey) {
            if (!this.selectedFile) {
                alert('Please select a file to upload.');
                return;
            }

            const formData = new FormData();
            formData.append('file', this.selectedFile);
            formData.append('item', itemKey);
            formData.append('user_role', userRole);

            try {
                const response = await fetch(`/api/transactions/${transactionId}/evidence`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Failed to upload evidence. Please try again.');
                }
            } catch (error) {
                console.error('Error uploading evidence:', error);
                alert('An error occurred. Please try again.');
            }
        }
    }
}
</script>
