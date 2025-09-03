<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'bidder_id',
        'bid_amount',
        'status',
        'bid_at',
        'outbid_at',
        'is_auto_bid',
        'max_auto_bid',
        'bidder_note',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'max_auto_bid' => 'decimal:2',
        'bid_at' => 'datetime',
        'outbid_at' => 'datetime',
        'is_auto_bid' => 'boolean',
    ];

    /**
     * Get the domain this bid is for.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the user who placed this bid.
     */
    public function bidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bidder_id');
    }

    /**
     * Check if this bid is currently the highest.
     */
    public function isHighest(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if this bid has been outbid.
     */
    public function isOutbid(): bool
    {
        return $this->status === 'outbid';
    }

    /**
     * Check if this bid won the auction.
     */
    public function isWinning(): bool
    {
        return $this->status === 'won';
    }

    /**
     * Get the formatted bid amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->bid_amount, 2);
    }

    /**
     * Get the formatted maximum auto-bid amount.
     */
    public function getFormattedMaxAutoBidAttribute(): string
    {
        if (!$this->max_auto_bid) {
            return 'N/A';
        }
        return '$' . number_format($this->max_auto_bid, 2);
    }

    /**
     * Get the time since the bid was placed.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->bid_at->diffForHumans();
    }

    /**
     * Scope to get only active bids.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only winning bids.
     */
    public function scopeWinning($query)
    {
        return $query->where('status', 'won');
    }

    /**
     * Scope to get bids for a specific domain.
     */
    public function scopeForDomain($query, $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Scope to get bids by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('bidder_id', $userId);
    }
}
