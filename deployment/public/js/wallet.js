/**
 * Wallet Management JavaScript
 * Handles dynamic wallet interactions and real-time updates
 */

class WalletManager {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.startAutoRefresh();
        this.checkWithdrawalEligibility();
    }

    bindEvents() {
        // Modal events
        document.getElementById('withdraw-form')?.addEventListener('submit', (e) => this.handleWithdraw(e));
        document.getElementById('add-funds-form')?.addEventListener('submit', (e) => this.handleAddFunds(e));
        
        // Filter events
        document.getElementById('type-filter')?.addEventListener('change', () => this.loadTransactions(true));
        document.getElementById('status-filter')?.addEventListener('change', () => this.loadTransactions(true));
        
        // Load more button
        document.getElementById('load-more-btn')?.addEventListener('click', () => this.loadTransactions());
        
        // Modal close events
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                this.closeAllModals();
            }
        });
    }

    // Modal Management
    openWithdrawModal() {
        document.getElementById('withdraw-modal')?.classList.remove('hidden');
        this.checkWithdrawalEligibility();
    }

    closeWithdrawModal() {
        document.getElementById('withdraw-modal')?.classList.add('hidden');
        document.getElementById('withdraw-form')?.reset();
    }

    openAddFundsModal() {
        document.getElementById('add-funds-modal')?.classList.remove('hidden');
    }

    closeAddFundsModal() {
        document.getElementById('add-funds-modal')?.classList.add('hidden');
        document.getElementById('add-funds-form')?.reset();
    }

    closeAllModals() {
        this.closeWithdrawModal();
        this.closeAddFundsModal();
    }

    // Withdrawal Management
    async checkWithdrawalEligibility() {
        try {
            const response = await fetch('/wallet/eligibility');
            const data = await response.json();
            
            const withdrawBtn = document.getElementById('withdraw-btn');
            const amountInput = document.getElementById('withdraw-amount');
            
            if (!data.can_withdraw) {
                if (withdrawBtn) {
                    withdrawBtn.disabled = true;
                    withdrawBtn.textContent = 'Withdrawal Not Available';
                    withdrawBtn.title = this.getEligibilityMessage(data);
                }
                if (amountInput) {
                    amountInput.max = 0;
                }
            } else {
                if (withdrawBtn) {
                    withdrawBtn.disabled = false;
                    withdrawBtn.textContent = 'Withdraw Funds';
                    withdrawBtn.title = '';
                }
                if (amountInput) {
                    amountInput.max = data.max_withdrawal;
                    amountInput.min = data.min_withdrawal;
                }
            }
        } catch (error) {
            console.error('Error checking withdrawal eligibility:', error);
        }
    }

    getEligibilityMessage(data) {
        if (!data.account_verified) return 'Account verification required';
        if (!data.paypal_verified) return 'PayPal verification required';
        if (data.current_balance < data.min_withdrawal) return 'Insufficient balance';
        return 'Withdrawal not available';
    }

    async handleWithdraw(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const submitBtn = e.target.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
        
        try {
            const response = await fetch('/wallet/withdraw', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeWithdrawModal();
                this.updateWalletBalance();
                this.loadTransactions(true);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Withdraw';
        }
    }

    async handleAddFunds(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const submitBtn = e.target.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adding...';
        
        try {
            const response = await fetch('/wallet/add-funds', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(data.message, 'success');
                this.closeAddFundsModal();
                this.updateWalletBalance();
                this.loadTransactions(true);
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Add Funds';
        }
    }

    // Balance Management
    async updateWalletBalance() {
        try {
            const response = await fetch('/wallet/balance');
            const data = await response.json();
            
            const balanceEl = document.getElementById('wallet-balance');
            const earningsEl = document.getElementById('total-earnings');
            const withdrawalsEl = document.getElementById('total-withdrawals');
            
            if (balanceEl) balanceEl.textContent = data.formatted_balance;
            if (earningsEl) earningsEl.textContent = '$' + parseFloat(data.total_earnings).toFixed(2);
            if (withdrawalsEl) withdrawalsEl.textContent = '$' + parseFloat(data.total_withdrawals).toFixed(2);
        } catch (error) {
            console.error('Error updating wallet balance:', error);
        }
    }

    // Transaction Management
    async loadTransactions(reset = false) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const loadingEl = document.getElementById('transactions-loading');
        const listEl = document.getElementById('transactions-list');
        
        if (reset) {
            this.currentPage = 1;
            if (listEl) listEl.innerHTML = '';
        }
        
        if (loadingEl) loadingEl.classList.remove('hidden');
        
        try {
            const typeFilter = document.getElementById('type-filter')?.value || '';
            const statusFilter = document.getElementById('status-filter')?.value || '';
            
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: 10
            });
            
            if (typeFilter) params.append('type', typeFilter);
            if (statusFilter) params.append('status', statusFilter);
            
            const response = await fetch(`/wallet/transactions?${params}`);
            const data = await response.json();
            
            if (reset && listEl) {
                listEl.innerHTML = '';
            }
            
            if (data.transactions && data.transactions.length > 0) {
                data.transactions.forEach(transaction => {
                    const transactionEl = this.createTransactionElement(transaction);
                    if (listEl) listEl.appendChild(transactionEl);
                });
                
                const loadMoreContainer = document.getElementById('load-more-container');
                if (loadMoreContainer) {
                    if (data.pagination.current_page < data.pagination.last_page) {
                        loadMoreContainer.classList.remove('hidden');
                    } else {
                        loadMoreContainer.classList.add('hidden');
                    }
                }
            } else if (reset && listEl) {
                listEl.innerHTML = this.getEmptyStateHTML();
            }
            
            this.currentPage++;
        } catch (error) {
            console.error('Error loading transactions:', error);
            this.showNotification('Failed to load transactions', 'error');
        } finally {
            this.isLoading = false;
            if (loadingEl) loadingEl.classList.add('hidden');
        }
    }

    createTransactionElement(transaction) {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg';
        
        const isCredit = transaction.type === 'credit';
        const iconColor = isCredit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        const bgColor = isCredit ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900';
        const amountColor = isCredit ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        
        const formattedAmount = (isCredit ? '+' : '-') + '$' + parseFloat(transaction.amount).toFixed(2);
        const statusClass = this.getStatusBadgeClass(transaction.status);
        const formattedDate = new Date(transaction.created_at).toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit' 
        });
        
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
                    <p class="text-xs text-gray-500 dark:text-gray-400">${formattedDate}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium ${amountColor}">${formattedAmount}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}</span>
            </div>
        `;
        
        return div;
    }

    getStatusBadgeClass(status) {
        switch(status) {
            case 'completed': return 'bg-green-100 text-green-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800';
            case 'failed': return 'bg-red-100 text-red-800';
            case 'cancelled': return 'bg-gray-100 text-gray-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    getEmptyStateHTML() {
        return `
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No transactions found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No transactions match your current filters.</p>
            </div>
        `;
    }

    // Export functionality
    exportTransactions() {
        window.open('/wallet/export?format=csv', '_blank');
    }

    // Auto-refresh functionality
    startAutoRefresh() {
        // Refresh balance every 30 seconds
        this.refreshInterval = setInterval(() => {
            this.updateWalletBalance();
        }, 30000);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    // Notification system
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ? 
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                            type === 'error' ?
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' :
                            '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
                        }
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Cleanup
    destroy() {
        this.stopAutoRefresh();
    }
}

// Global functions for backward compatibility
let walletManager;

document.addEventListener('DOMContentLoaded', function() {
    walletManager = new WalletManager();
});

// Global functions
function openWithdrawModal() {
    walletManager?.openWithdrawModal();
}

function closeWithdrawModal() {
    walletManager?.closeWithdrawModal();
}

function openAddFundsModal() {
    walletManager?.openAddFundsModal();
}

function closeAddFundsModal() {
    walletManager?.closeAddFundsModal();
}

function exportTransactions() {
    walletManager?.exportTransactions();
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    walletManager?.destroy();
});
