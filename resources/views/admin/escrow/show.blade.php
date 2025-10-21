@extends('layouts.admin')

@section('title', 'Transaction Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Transaction #{{ $transaction->id }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $transaction->domain->full_domain }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.escrow.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transaction Details -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Transaction Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction ID</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">#{{ $transaction->id }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_badge_class }}">
                                    {{ $transaction->status_text }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->formatted_amount }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platform Fee</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->currency }} {{ number_format($transaction->fee_amount, 2) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Net Amount (Seller)</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $transaction->formatted_net_amount }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($transaction->provider) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider Transaction ID</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $transaction->provider_txn_id ?: 'N/A' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created At</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            
                            @if($transaction->escrow_released_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Released At</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->escrow_released_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                            
                            @if($transaction->refunded_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refunded At</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->refunded_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Domain Transfer Details -->
                @if($transaction->domainTransfer)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Domain Transfer</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transfer Method</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->domainTransfer->transfer_method_display }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->domainTransfer->verification_badge_class }}">
                                        {{ $transaction->domainTransfer->verification_status_text }}
                                    </span>
                                </div>
                                
                                @if($transaction->domainTransfer->transfer_notes)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transfer Notes</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->domainTransfer->transfer_notes }}</p>
                                </div>
                                @endif
                                
                                @if($transaction->domainTransfer->verification_notes)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verification Notes</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->domainTransfer->verification_notes }}</p>
                                </div>
                                @endif
                                
                                @if($transaction->domainTransfer->evidence_url)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Evidence</label>
                                    <a href="{{ $transaction->domainTransfer->evidence_url }}" target="_blank" 
                                       class="mt-1 text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Evidence
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Audit Log -->
                @if($transaction->audits->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Audit Log</h3>
                            
                            <div class="space-y-4">
                                @foreach($transaction->audits as $audit)
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $audit->event_display }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $audit->description }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $audit->formatted_created_at }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $audit->user_type_display }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions Sidebar -->
            <div class="lg:col-span-1">
                <!-- User Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Parties</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Buyer</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->buyer->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->buyer->email }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seller</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $transaction->seller->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->seller->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($transaction->isInEscrow())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                            
                            <div class="space-y-3">
                                @if($transaction->domainTransfer && $transaction->domainTransfer->isVerified())
                                    <form method="POST" action="{{ route('admin.escrow.release', $transaction) }}" 
                                          onsubmit="return confirm('Are you sure you want to release escrow funds? This action cannot be undone.')">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Release Notes</label>
                                            <textarea name="verification_notes" rows="3" 
                                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                      placeholder="Optional notes about the release..."></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                            Release Escrow
                                        </button>
                                    </form>
                                @else
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Domain transfer must be verified before releasing escrow.
                                    </p>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.escrow.refund', $transaction) }}" 
                                      onsubmit="return confirm('Are you sure you want to refund this transaction? This action cannot be undone.')">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Refund Reason</label>
                                        <textarea name="refund_reason" rows="3" required
                                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                  placeholder="Reason for refund..."></textarea>
                                    </div>
                                    <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Refund Transaction
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Transfer Verification -->
                @if($transaction->domainTransfer && !$transaction->domainTransfer->isVerified())
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Verify Transfer</h3>
                            
                            <form method="POST" action="{{ route('admin.escrow.verify-transfer', $transaction->domainTransfer) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Verification Notes</label>
                                    <textarea name="verification_notes" rows="3" 
                                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                              placeholder="Notes about the verification..."></textarea>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    Verify Transfer
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
