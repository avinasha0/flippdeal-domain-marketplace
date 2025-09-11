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
        'user_id',
        'amount',
        'is_winning',
        'is_outbid',
        'bid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bid_at' => 'datetime',
        'is_winning' => 'boolean',
        'is_outbid' => 'boolean',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this bid is currently the highest.
     */
    public function isHighest(): bool
    {
        return $this->is_winning;
    }

    /**
     * Check if this bid has been outbid.
     */
    public function isOutbid(): bool
    {
        return $this->is_outbid;
    }

    /**
     * Check if this bid won the auction.
     */
    public function isWinning(): bool
    {
        return $this->is_winning;
    }

    /**
     * Get the formatted bid amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get the time since the bid was placed.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->bid_at->diffForHumans();
    }

    /**
     * Scope to get only winning bids.
     */
    public function scopeWinning($query)
    {
        return $query->where('is_winning', true);
    }

    /**
     * Scope to get only outbid bids.
     */
    public function scopeOutbid($query)
    {
        return $query->where('is_outbid', true);
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
        return $query->where('user_id', $userId);
    }
}
