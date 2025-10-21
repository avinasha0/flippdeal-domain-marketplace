<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_id',
        'notes',
        'notify_on_price_change',
        'notify_on_status_change'
    ];

    protected $casts = [
        'notify_on_price_change' => 'boolean',
        'notify_on_status_change' => 'boolean'
    ];

    /**
     * Get the user who favorited this domain.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the domain that was favorited.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Toggle price change notifications.
     */
    public function togglePriceNotifications(): void
    {
        $this->update([
            'notify_on_price_change' => !$this->notify_on_price_change
        ]);
    }

    /**
     * Toggle status change notifications.
     */
    public function toggleStatusNotifications(): void
    {
        $this->update([
            'notify_on_status_change' => !$this->notify_on_status_change
        ]);
    }

    /**
     * Check if user should be notified of price changes.
     */
    public function shouldNotifyPriceChange(): bool
    {
        return $this->notify_on_price_change;
    }

    /**
     * Check if user should be notified of status changes.
     */
    public function shouldNotifyStatusChange(): bool
    {
        return $this->notify_on_status_change;
    }

    /**
     * Scope a query to only include favorites for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include favorites for a specific domain.
     */
    public function scopeForDomain($query, int $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    /**
     * Scope a query to only include favorites with price change notifications enabled.
     */
    public function scopeWithPriceNotifications($query)
    {
        return $query->where('notify_on_price_change', true);
    }

    /**
     * Scope a query to only include favorites with status change notifications enabled.
     */
    public function scopeWithStatusNotifications($query)
    {
        return $query->where('notify_on_status_change', true);
    }
}
