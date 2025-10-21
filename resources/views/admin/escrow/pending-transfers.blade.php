@extends('layouts.admin')

@section('title', 'Pending Transfers')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Pending Transfers</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Domain transfers awaiting verification</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.escrow.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Back to Escrow
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfers Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($transfers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Domain</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">From</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Submitted</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($transfers as $transfer)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $transfer->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transfer->domain->full_domain }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transfer->fromUser->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transfer->toUser->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transfer->transfer_method_display }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transfer->created_at->format('M j, Y g:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button onclick="showTransferDetails({{ $transfer->id }})" 
                                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    View
                                                </button>
                                                <form method="POST" action="{{ route('admin.escrow.verify-transfer', $transfer) }}" 
                                                      onsubmit="return confirm('Are you sure you want to verify this transfer?')" 
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                        Verify
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transfers->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No pending transfers</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All domain transfers have been verified.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transfer Details Modal -->
<div id="transferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Transfer Details</h3>
                <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="transferDetails" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeTransferModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium">
                    Close
                </button>
                <form id="verifyForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Verify Transfer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTransferDetails(transferId) {
    // This would typically make an AJAX call to get transfer details
    // For now, we'll show a placeholder
    document.getElementById('transferDetails').innerHTML = `
        <div class="text-center py-8">
            <p class="text-gray-600 dark:text-gray-400">Loading transfer details...</p>
        </div>
    `;
    
    document.getElementById('verifyForm').action = `/admin/escrow/transfers/${transferId}/verify`;
    document.getElementById('transferModal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
}
</script>
@endsection
