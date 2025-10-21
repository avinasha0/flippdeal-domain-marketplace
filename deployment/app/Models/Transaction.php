<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'domain_id',
        'amount',
        'fee_amount',
        'currency',
        'provider',
        'provider_txn_id',
        'escrow_state',
        'escrow_metadata',
        'escrow_release_by_admin_id',
        'escrow_released_at',
        'refunded_at',
        'refund_reason',
        'kyc_required',
        'kyc_approved',
        'kyc_request_id',
        'kyc_approved_at',
        'seller_checklist',
        'buyer_checklist',
        'transfer_evidence',
        'transfer_initiated_at',
        'transfer_completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'escrow_metadata' => 'array',
        'escrow_released_at' => 'datetime',
        'refunded_at' => 'datetime',
        'kyc_required' => 'boolean',
        'kyc_approved' => 'boolean',
        'kyc_approved_at' => 'datetime',
        'seller_checklist' => 'array',
        'buyer_checklist' => 'array',
        'transfer_evidence' => 'array',
        'transfer_initiated_at' => 'datetime',
        'transfer_completed_at' => 'datetime',
    ];

    // Escrow states
    const STATE_PENDING = 'pending';
    const STATE_IN_ESCROW = 'in_escrow';
    const STATE_RELEASED = 'released';
    const STATE_REFUNDED = 'refunded';
    const STATE_CANCELLED = 'cancelled';

    /**
     * Get the buyer user.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller user.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the admin who released the escrow.
     */
    public function releasedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escrow_release_by_admin_id');
    }

    /**
     * Get the domain transfer record.
     */
    public function domainTransfer(): HasOne
    {
        return $this->hasOne(DomainTransfer::class);
    }

    /**
     * Get the KYC request for this transaction.
     */
    public function kycRequest(): BelongsTo
    {
        return $this->belongsTo(KycRequest::class);
    }

    /**
     * Get audit logs for this transaction.
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    /**
     * Check if transaction is in escrow.
     */
    public function isInEscrow(): bool
    {
        return $this->escrow_state === self::STATE_IN_ESCROW;
    }

    /**
     * Check if transaction is released.
     */
    public function isReleased(): bool
    {
        return $this->escrow_state === self::STATE_RELEASED;
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->escrow_state === self::STATE_REFUNDED;
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->escrow_state === self::STATE_PENDING;
    }

    /**
     * Check if transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->escrow_state === self::STATE_CANCELLED;
    }

    /**
     * Get the net amount for seller (amount - fee).
     */
    public function getNetAmountAttribute(): float
    {
        return $this->amount - $this->fee_amount;
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted net amount with currency.
     */
    public function getFormattedNetAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->net_amount, 2);
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->escrow_state) {
            self::STATE_PENDING => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATE_IN_ESCROW => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::STATE_RELEASED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATE_REFUNDED => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::STATE_CANCELLED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    /**
     * Get human readable status.
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->escrow_state) {
            self::STATE_PENDING => 'Pending Payment',
            self::STATE_IN_ESCROW => 'In Escrow',
            self::STATE_RELEASED => 'Released',
            self::STATE_REFUNDED => 'Refunded',
            self::STATE_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Boot method to log state changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($transaction) {
            if ($transaction->isDirty('escrow_state')) {
                $oldState = $transaction->getOriginal('escrow_state');
                $newState = $transaction->escrow_state;
                
                // Log the state change
                $transaction->audits()->create([
                    'event' => 'state_changed',
                    'old_values' => ['escrow_state' => $oldState],
                    'new_values' => ['escrow_state' => $newState],
                    'user_id' => auth()->id(),
                    'user_type' => auth()->user()?->isAdmin() ? 'admin' : 'user',
                    'description' => "Transaction state changed from {$oldState} to {$newState}",
                ]);
            }
        });
    }
}