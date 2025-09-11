<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'domain_id',
        'buyer_id',
        'seller_id',
        'domain_price',
        'commission_amount',
        'total_amount',
        'seller_amount',
        'status',
        'payment_method',
        'purchase_type',
        'payment_transaction_id',
        'paid_at',
        'escrow_released_at',
        'completed_at',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'domain_price' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'escrow_released_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
        'purchase_type' => 'string'
    ];

    /**
     * Boot the model and generate order number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the domain associated with this order.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the buyer (user who purchased).
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller (user who owns the domain).
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the payment transactions for this order.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the messages related to this order.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Calculate commission amount based on domain price.
     */
    public function calculateCommission(): float
    {
        $commissionRate = $this->domain->commission_rate ?? 5.00; // Default 5%
        return round(($this->domain_price * $commissionRate) / 100, 2);
    }

    /**
     * Calculate total amount including commission.
     */
    public function calculateTotalAmount(): float
    {
        return $this->domain_price + $this->commission_amount;
    }

    /**
     * Calculate amount seller receives after commission.
     */
    public function calculateSellerAmount(): float
    {
        return $this->domain_price - $this->commission_amount;
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    /**
     * Check if order can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'in_escrow';
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(string $paymentMethod, string $transactionId): void
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_transaction_id' => $transactionId,
            'paid_at' => now()
        ]);
    }

    /**
     * Move order to escrow.
     */
    public function moveToEscrow(): void
    {
        $this->update([
            'status' => 'in_escrow'
        ]);
    }

    /**
     * Complete the order and release escrow.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Update domain status
        $this->domain->update(['status' => 'sold']);
    }

    /**
     * Scope a query to only include orders with specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include orders for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)
              ->orWhere('seller_id', $userId);
        });
    }

    /**
     * Get the human-readable purchase type.
     */
    public function getPurchaseTypeLabelAttribute(): string
    {
        return match($this->purchase_type) {
            'bin' => 'Buy It Now',
            'asking_price' => 'Asking Price',
            'offer' => 'Offer',
            default => 'Unknown'
        };
    }
}
