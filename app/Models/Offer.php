<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'buyer_id',
        'offer_amount',
        'message',
        'status',
        'expires_at',
        'responded_at',
        'seller_response',
        'metadata'
    ];

    protected $casts = [
        'offer_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'responded_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Get the domain this offer is for.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the buyer who made the offer.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller who owns the domain.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the seller through the domain relationship.
     */
    public function getSellerAttribute(): User
    {
        return $this->domain->user;
    }

    /**
     * Check if the offer is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    /**
     * Check if the offer can be accepted.
     */
    public function canBeAccepted(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Check if the offer can be rejected.
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Check if the offer can be withdrawn.
     */
    public function canBeWithdrawn(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Accept the offer.
     */
    public function accept(string $responseMessage = null): void
    {
        $this->update([
            'status' => 'accepted',
            'seller_response' => $responseMessage,
            'responded_at' => now()
        ]);

        // Update domain offer count
        $this->domain->increment('offer_count');
    }

    /**
     * Reject the offer.
     */
    public function reject(string $responseMessage = null): void
    {
        $this->update([
            'status' => 'rejected',
            'seller_response' => $responseMessage,
            'responded_at' => now()
        ]);
    }

    /**
     * Withdraw the offer.
     */
    public function withdraw(): void
    {
        $this->update([
            'status' => 'withdrawn'
        ]);
    }

    /**
     * Convert offer to order.
     */
    public function convertToOrder(): Order
    {
        $this->update(['status' => 'converted']);

        return Order::create([
            'domain_id' => $this->domain_id,
            'buyer_id' => $this->buyer_id,
            'seller_id' => $this->domain->user_id,
            'domain_price' => $this->offer_amount,
            'commission_amount' => $this->offer_amount * ($this->domain->commission_rate ?? 5.00) / 100,
            'total_amount' => $this->offer_amount * (1 + ($this->domain->commission_rate ?? 5.00) / 100),
            'seller_amount' => $this->offer_amount * (1 - ($this->domain->commission_rate ?? 5.00) / 100),
            'status' => 'pending',
            'notes' => 'Converted from offer #' . $this->id
        ]);
    }

    /**
     * Scope a query to only include offers with specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include active (non-expired) offers.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope a query to only include offers for a specific domain.
     */
    public function scopeForDomain($query, int $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Scope a query to only include offers from a specific user.
     */
    public function scopeFromUser($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    /**
     * Scope a query to only include offers for domains owned by a specific user.
     */
    public function scopeForUserDomains($query, int $userId)
    {
        return $query->whereHas('domain', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
