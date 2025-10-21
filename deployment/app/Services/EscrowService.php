<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\DomainTransfer;
use App\Models\Domain;
use App\Models\User;
use App\Events\TransactionCreated;
use App\Events\TransactionInEscrow;
use App\Events\TransactionReleased;
use App\Events\TransactionRefunded;
use App\Jobs\ProcessPayout;
use App\Jobs\ProcessRefund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EscrowService
{
    /**
     * Create a new escrow transaction.
     */
    public function createEscrowTransaction(
        User $buyer,
        User $seller,
        Domain $domain,
        float $amount,
        string $provider = 'paypal',
        array $metadata = []
    ): Transaction {
        // Calculate platform fee (e.g., 5% of transaction amount)
        $feeAmount = $amount * 0.05;
        
        $transaction = Transaction::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'domain_id' => $domain->id,
            'amount' => $amount,
            'fee_amount' => $feeAmount,
            'currency' => 'USD',
            'provider' => $provider,
            'escrow_state' => Transaction::STATE_PENDING,
            'escrow_metadata' => $metadata,
        ]);

        // Log the transaction creation
        $transaction->audits()->create([
            'event' => 'created',
            'new_values' => $transaction->toArray(),
            'user_id' => $buyer->id,
            'user_type' => 'user',
            'description' => 'Escrow transaction created',
            'metadata' => [
                'domain_name' => $domain->full_domain,
                'seller_name' => $seller->name,
            ],
        ]);

        // Fire event
        event(new TransactionCreated($transaction));

        return $transaction;
    }

    /**
     * Mark transaction as in escrow after payment confirmation.
     */
    public function markInEscrow(
        Transaction $transaction,
        string $providerTxnId,
        array $metadata = []
    ): Transaction {
        if ($transaction->escrow_state !== Transaction::STATE_PENDING) {
            throw new \Exception('Transaction must be in pending state to mark as in escrow');
        }

        DB::transaction(function () use ($transaction, $providerTxnId, $metadata) {
            $transaction->update([
                'escrow_state' => Transaction::STATE_IN_ESCROW,
                'provider_txn_id' => $providerTxnId,
                'escrow_metadata' => array_merge($transaction->escrow_metadata ?? [], $metadata),
            ]);

            // Update domain status to sold
            $transaction->domain->update(['status' => 'sold']);

            // Log the state change
            $transaction->audits()->create([
                'event' => 'payment_received',
                'old_values' => ['escrow_state' => Transaction::STATE_PENDING],
                'new_values' => ['escrow_state' => Transaction::STATE_IN_ESCROW],
                'user_id' => null,
                'user_type' => 'system',
                'description' => 'Payment received and held in escrow',
                'metadata' => [
                    'provider_txn_id' => $providerTxnId,
                ],
            ]);
        });

        // Fire event
        event(new TransactionInEscrow($transaction));

        return $transaction->fresh();
    }

    /**
     * Release escrow funds to seller.
     */
    public function releaseEscrow(
        Transaction $transaction,
        ?int $releasedByAdminId = null,
        array $metadata = []
    ): Transaction {
        if ($transaction->escrow_state !== Transaction::STATE_IN_ESCROW) {
            throw new \Exception('Transaction must be in escrow to release funds');
        }

        if (!$this->validateReleaseConditions($transaction)) {
            throw new \Exception('Release conditions not met');
        }

        DB::transaction(function () use ($transaction, $releasedByAdminId, $metadata) {
            $transaction->update([
                'escrow_state' => Transaction::STATE_RELEASED,
                'escrow_release_by_admin_id' => $releasedByAdminId,
                'escrow_released_at' => now(),
                'escrow_metadata' => array_merge($transaction->escrow_metadata ?? [], $metadata),
            ]);

            // Log the release
            $transaction->audits()->create([
                'event' => 'escrow_released',
                'old_values' => ['escrow_state' => Transaction::STATE_IN_ESCROW],
                'new_values' => ['escrow_state' => Transaction::STATE_RELEASED],
                'user_id' => $releasedByAdminId,
                'user_type' => $releasedByAdminId ? 'admin' : 'system',
                'description' => 'Escrow funds released to seller',
                'metadata' => $metadata,
            ]);
        });

        // Queue payout job
        ProcessPayout::dispatch($transaction);

        // Fire event
        event(new TransactionReleased($transaction));

        return $transaction->fresh();
    }

    /**
     * Refund escrow funds to buyer.
     */
    public function refundEscrow(
        Transaction $transaction,
        string $reason,
        array $metadata = []
    ): Transaction {
        if (!in_array($transaction->escrow_state, [Transaction::STATE_IN_ESCROW, Transaction::STATE_PENDING])) {
            throw new \Exception('Transaction must be in escrow or pending to refund');
        }

        DB::transaction(function () use ($transaction, $reason, $metadata) {
            $transaction->update([
                'escrow_state' => Transaction::STATE_REFUNDED,
                'refunded_at' => now(),
                'refund_reason' => $reason,
                'escrow_metadata' => array_merge($transaction->escrow_metadata ?? [], $metadata),
            ]);

            // Revert domain status if it was sold
            if ($transaction->domain->status === 'sold') {
                $transaction->domain->update(['status' => 'active']);
            }

            // Log the refund
            $transaction->audits()->create([
                'event' => 'refund_initiated',
                'old_values' => ['escrow_state' => $transaction->getOriginal('escrow_state')],
                'new_values' => ['escrow_state' => Transaction::STATE_REFUNDED],
                'user_id' => auth()->id(),
                'user_type' => auth()->user()?->isAdmin() ? 'admin' : 'user',
                'description' => "Refund initiated: {$reason}",
                'metadata' => array_merge($metadata, ['reason' => $reason]),
            ]);
        });

        // Queue refund job
        ProcessRefund::dispatch($transaction);

        // Fire event
        event(new TransactionRefunded($transaction));

        return $transaction->fresh();
    }

    /**
     * Cancel a pending transaction.
     */
    public function cancelTransaction(
        Transaction $transaction,
        string $reason,
        array $metadata = []
    ): Transaction {
        if ($transaction->escrow_state !== Transaction::STATE_PENDING) {
            throw new \Exception('Only pending transactions can be cancelled');
        }

        DB::transaction(function () use ($transaction, $reason, $metadata) {
            $transaction->update([
                'escrow_state' => Transaction::STATE_CANCELLED,
                'escrow_metadata' => array_merge($transaction->escrow_metadata ?? [], $metadata),
            ]);

            // Log the cancellation
            $transaction->audits()->create([
                'event' => 'cancelled',
                'old_values' => ['escrow_state' => Transaction::STATE_PENDING],
                'new_values' => ['escrow_state' => Transaction::STATE_CANCELLED],
                'user_id' => auth()->id(),
                'user_type' => auth()->user()?->isAdmin() ? 'admin' : 'user',
                'description' => "Transaction cancelled: {$reason}",
                'metadata' => array_merge($metadata, ['reason' => $reason]),
            ]);
        });

        return $transaction->fresh();
    }

    /**
     * Validate release conditions for a transaction.
     */
    public function validateReleaseConditions(Transaction $transaction): bool
    {
        // Check if domain transfer is verified
        $transfer = $transaction->domainTransfer;
        if (!$transfer || !$transfer->isVerified()) {
            Log::info('Release conditions not met: Domain transfer not verified', [
                'transaction_id' => $transaction->id,
                'domain_id' => $transaction->domain_id,
            ]);
            return false;
        }

        // Check if domain status is sold
        if ($transaction->domain->status !== 'sold') {
            Log::info('Release conditions not met: Domain not sold', [
                'transaction_id' => $transaction->id,
                'domain_status' => $transaction->domain->status,
            ]);
            return false;
        }

        // Additional business logic checks can be added here
        // e.g., dispute resolution, time-based holds, etc.

        return true;
    }

    /**
     * Create a domain transfer record.
     */
    public function createDomainTransfer(
        Transaction $transaction,
        string $transferMethod,
        array $evidenceData = [],
        ?string $evidenceUrl = null,
        ?string $transferNotes = null
    ): DomainTransfer {
        $transfer = DomainTransfer::create([
            'domain_id' => $transaction->domain_id,
            'transaction_id' => $transaction->id,
            'from_user_id' => $transaction->seller_id,
            'to_user_id' => $transaction->buyer_id,
            'transfer_method' => $transferMethod,
            'evidence_data' => $evidenceData,
            'evidence_url' => $evidenceUrl,
            'transfer_notes' => $transferNotes,
        ]);

        // Log the transfer creation
        $transfer->audits()->create([
            'event' => 'created',
            'new_values' => $transfer->toArray(),
            'user_id' => $transaction->seller_id,
            'user_type' => 'user',
            'description' => 'Domain transfer evidence submitted',
            'metadata' => [
                'transfer_method' => $transferMethod,
                'domain_name' => $transaction->domain->full_domain,
            ],
        ]);

        return $transfer;
    }

    /**
     * Verify a domain transfer.
     */
    public function verifyDomainTransfer(
        DomainTransfer $transfer,
        ?int $verifiedByAdminId = null,
        ?string $verificationNotes = null
    ): DomainTransfer {
        $transfer->update([
            'verified' => true,
            'verified_by_admin_id' => $verifiedByAdminId,
            'verified_at' => now(),
            'verification_notes' => $verificationNotes,
        ]);

        // Log the verification
        $transfer->audits()->create([
            'event' => 'verification_changed',
            'old_values' => ['verified' => false],
            'new_values' => ['verified' => true],
            'user_id' => $verifiedByAdminId,
            'user_type' => $verifiedByAdminId ? 'admin' : 'system',
            'description' => 'Domain transfer verified',
            'metadata' => [
                'verification_notes' => $verificationNotes,
                'domain_name' => $transfer->domain->full_domain,
            ],
        ]);

        // If this is an automated verification, try to release escrow
        if (!$verifiedByAdminId) {
            $transaction = $transfer->transaction;
            if ($transaction->isInEscrow() && $this->validateReleaseConditions($transaction)) {
                $this->releaseEscrow($transaction, null, ['auto_released' => true]);
            }
        }

        return $transfer->fresh();
    }

    /**
     * Get escrow statistics for admin dashboard.
     */
    public function getEscrowStatistics(): array
    {
        return [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('escrow_state', Transaction::STATE_PENDING)->count(),
            'in_escrow_transactions' => Transaction::where('escrow_state', Transaction::STATE_IN_ESCROW)->count(),
            'released_transactions' => Transaction::where('escrow_state', Transaction::STATE_RELEASED)->count(),
            'refunded_transactions' => Transaction::where('escrow_state', Transaction::STATE_REFUNDED)->count(),
            'total_escrow_amount' => Transaction::where('escrow_state', Transaction::STATE_IN_ESCROW)->sum('amount'),
            'total_fees_collected' => Transaction::where('escrow_state', Transaction::STATE_RELEASED)->sum('fee_amount'),
            'pending_transfers' => DomainTransfer::where('verified', false)->count(),
        ];
    }
}
