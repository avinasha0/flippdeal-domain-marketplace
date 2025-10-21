<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'status',
        'reference_id',
        'metadata',
        'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    // Transaction types
    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    // Transaction statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        $prefix = $this->type === self::TYPE_CREDIT ? '+' : '-';
        return $prefix . '$' . number_format($this->amount, 2);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Mark transaction as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now()
        ]);
    }

    /**
     * Mark transaction as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'processed_at' => now()
        ]);
    }
}
