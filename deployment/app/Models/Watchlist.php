<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_id',
    ];

    /**
     * Get the user that owns the watchlist item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the domain that is being watched.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Check if a user is watching a domain.
     */
    public static function isWatching(int $userId, int $domainId): bool
    {
        return self::where('user_id', $userId)
            ->where('domain_id', $domainId)
            ->exists();
    }

    /**
     * Add a domain to user's watchlist.
     */
    public static function addToWatchlist(int $userId, int $domainId): self
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'domain_id' => $domainId,
        ]);
    }

    /**
     * Remove a domain from user's watchlist.
     */
    public static function removeFromWatchlist(int $userId, int $domainId): bool
    {
        return self::where('user_id', $userId)
            ->where('domain_id', $domainId)
            ->delete() > 0;
    }

    /**
     * Toggle watchlist status for a domain.
     */
    public static function toggleWatchlist(int $userId, int $domainId): bool
    {
        if (self::isWatching($userId, $domainId)) {
            return self::removeFromWatchlist($userId, $domainId);
        } else {
            self::addToWatchlist($userId, $domainId);
            return true;
        }
    }

    /**
     * Get watchlist count for a user.
     */
    public static function getWatchlistCount(int $userId): int
    {
        return self::where('user_id', $userId)->count();
    }

    /**
     * Scope a query to only include watchlist items for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include watchlist items for a specific domain.
     */
    public function scopeForDomain($query, int $domainId)
    {
        return $query->where('domain_id', $domainId);
    }
}