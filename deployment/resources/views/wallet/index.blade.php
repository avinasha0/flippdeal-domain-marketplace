@extends('layouts.app')

@section('title', 'Wallet & Earnings')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Wallet & Earnings</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your funds and view transaction history</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Wallet Balance Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Available Balance</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2" id="wallet-balance">
                                ${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Current Balance</p>
                            
                            <!-- Quick Stats -->
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Earnings:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400" id="total-earnings">
                                        ${{ number_format(auth()->user()->total_earnings ?? 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Withdrawals:</span>
                                    <span class="font-medium text-red-600 dark:text-red-400" id="total-withdrawals">
                                        ${{ number_format(auth()->user()->total_withdrawals ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-6 space-y-3">
                            <button onclick="openWithdrawModal()" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    id="withdraw-btn">
                                Withdraw Funds
                            </button>
                            <button onclick="openAddFundsModal()" 
                                    class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-3 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Add Funds
                            </button>
                            <button onclick="exportTransactions()" 
                                    class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium py-3 px-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Export History
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Transaction History</h3>
                            <div class="flex space-x-2">
                                <select id="type-filter" class="text-sm border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">All Types</option>
                                    <option value="credit">Credits</option>
                                    <option value="debit">Debits</option>
                                </select>
                                <select id="status-filter" class="text-sm border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">All Statuses</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Loading State -->
                        <div id="transactions-loading" class="text-center py-8 hidden">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">Loading transactions...</p>
                        </div>

                        <!-- Transactions List -->
                        <div id="transactions-list" class="space-y-4">
                            @forelse($recentTransactions as $transaction)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($transaction->type === 'credit')
                                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M j, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->formatted_amount }}
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->status_badge_class }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No transactions</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You haven't made any transactions yet.</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Load More Button -->
                        <div id="load-more-container" class="mt-6 text-center hidden">
                            <button id="load-more-btn" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Load More Transactions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdraw-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Withdraw Funds</h3>
                <button onclick="closeWithdrawModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="withdraw-form">
                <div class="mb-4">
                    <label for="withdraw-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                               id="withdraw-amount" 
                               name="amount" 
                               step="0.01" 
                               min="10" 
                               max="10000" 
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                               placeholder="0.00">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimum: $10.00 | Maximum: $10,000.00</p>
                </div>
                
                <div class="mb-4">
                    <label for="withdraw-paypal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PayPal Email</label>
                    <input type="email" 
                           id="withdraw-paypal" 
                           name="paypal_email" 
                           value="{{ auth()->user()->paypal_email }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                           readonly>
                    <p class="mt-1 text-xs text-gray-500">Funds will be sent to your verified PayPal email</p>
                </div>
                
                <div class="mb-6">
                    <label for="withdraw-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
                    <input type="text" 
                           id="withdraw-description" 
                           name="description" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                           placeholder="Withdrawal description">
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeWithdrawModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Withdraw
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Funds Modal -->
<div id="add-funds-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Funds</h3>
                <button onclick="closeAddFundsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="add-funds-form">
                <div class="mb-4">
                    <label for="add-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                               id="add-amount" 
                               name="amount" 
                               step="0.01" 
                               min="1" 
                               max="10000" 
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                               placeholder="0.00">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Minimum: $1.00 | Maximum: $10,000.00</p>
                </div>
                
                <div class="mb-6">
                    <label for="add-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
                    <input type="text" 
                           id="add-description" 
                           name="description" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" 
                           placeholder="Funds added to wallet">
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeAddFundsModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        Add Funds
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/wallet.js') }}"></script>
<script>
// Wallet functionality
let currentPage = 1;
let isLoading = false;

// Modal functions
function openWithdrawModal() {
    document.getElementById('withdraw-modal').classList.remove('hidden');
    checkWithdrawalEligibility();
}

function closeWithdrawModal() {
    document.getElementById('withdraw-modal').classList.add('hidden');
    document.getElementById('withdraw-form').reset();
}

function openAddFundsModal() {
    document.getElementById('add-funds-modal').classList.remove('hidden');
}

function closeAddFundsModal() {
    document.getElementById('add-funds-modal').classList.add('hidden');
    document.getElementById('add-funds-form').reset();
}

// Check withdrawal eligibility
async function checkWithdrawalEligibility() {
    try {
        const response = await fetch('/wallet/eligibility');
        const data = await response.json();
        
        const withdrawBtn = document.getElementById('withdraw-btn');
        const amountInput = document.getElementById('withdraw-amount');
        
        if (!data.can_withdraw) {
            withdrawBtn.disabled = true;
            withdrawBtn.textContent = 'Withdrawal Not Available';
            amountInput.max = 0;
        } else {
            withdrawBtn.disabled = false;
            withdrawBtn.textContent = 'Withdraw Funds';
            amountInput.max = data.max_withdrawal;
            amountInput.min = data.min_withdrawal;
        }
    } catch (error) {
        console.error('Error checking withdrawal eligibility:', error);
    }
}

// Withdraw form submission
document.getElementById('withdraw-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    try {
        const response = await fetch('/wallet/withdraw', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeWithdrawModal();
            updateWalletBalance();
            loadTransactions();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Withdraw';
    }
});

// Add funds form submission
document.getElementById('add-funds-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    try {
        const response = await fetch('/wallet/add-funds', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeAddFundsModal();
            updateWalletBalance();
            loadTransactions();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Add Funds';
    }
});

// Update wallet balance
async function updateWalletBalance() {
    try {
        const response = await fetch('/wallet/balance');
        const data = await response.json();
        
        document.getElementById('wallet-balance').textContent = data.formatted_balance;
        document.getElementById('total-earnings').textContent = '$' + parseFloat(data.total_earnings).toFixed(2);
        document.getElementById('total-withdrawals').textContent = '$' + parseFloat(data.total_withdrawals).toFixed(2);
    } catch (error) {
        console.error('Error updating wallet balance:', error);
    }
}

// Load transactions
async function loadTransactions(reset = false) {
    if (isLoading) return;
    
    isLoading = true;
    const loadingEl = document.getElementById('transactions-loading');
    const listEl = document.getElementById('transactions-list');
    
    if (reset) {
        currentPage = 1;
        listEl.innerHTML = '';
    }
    
    loadingEl.classList.remove('hidden');
    
    try {
        const typeFilter = document.getElementById('type-filter').value;
        const statusFilter = document.getElementById('status-filter').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            per_page: 10
        });
        
        if (typeFilter) params.append('type', typeFilter);
        if (statusFilter) params.append('status', statusFilter);
        
        const response = await fetch(`/wallet/transactions?${params}`);
        const data = await response.json();
        
        if (reset) {
            listEl.innerHTML = '';
        }
        
        if (data.transactions.length > 0) {
            data.transactions.forEach(transaction => {
                const transactionEl = createTransactionElement(transaction);
                listEl.appendChild(transactionEl);
            });
            
            if (data.pagination.current_page < data.pagination.last_page) {
                document.getElementById('load-more-container').classList.remove('hidden');
            } else {
                document.getElementById('load-more-container').classList.add('hidden');
            }
        } else if (reset) {
            listEl.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No transactions found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No transactions match your current filters.</p>
                </div>
            `;
        }
        
        currentPage++;
    } catch (error) {
        console.error('Error loading transactions:', error);
    } finally {
        isLoading = false;
        loadingEl.classList.add('hidden');
    }
}

// Create transaction element
function createTransactionElement(transaction) {
    const div = document.createElement('div');
    div.className = 'flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg';
    
    const isCredit = transaction.type === 'credit';
    const iconColor = isCredit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
    const bgColor = isCredit ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900';
    const amountColor = isCredit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
    
    const formattedAmount = (isCredit ? '+' : '-') + '$' + parseFloat(transaction.amount).toFixed(2);
    const statusClass = getStatusBadgeClass(transaction.status);
    
    div.innerHTML = `
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 ${bgColor} rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${isCredit ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>'
                        }
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">${transaction.description}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">${new Date(transaction.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm font-medium ${amountColor}">${formattedAmount}</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span>
        </div>
    `;
    
    return div;
}

// Get status badge class
function getStatusBadgeClass(status) {
    switch(status) {
        case 'completed': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'failed': return 'bg-red-100 text-red-800';
        case 'cancelled': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Export transactions
function exportTransactions() {
    window.open('/wallet/export?format=csv', '_blank');
}

// Event listeners
document.getElementById('load-more-btn').addEventListener('click', () => loadTransactions());
document.getElementById('type-filter').addEventListener('change', () => loadTransactions(true));
document.getElementById('status-filter').addEventListener('change', () => loadTransactions(true));

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkWithdrawalEligibility();
});
</script>
@endpush
