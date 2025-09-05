<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain_name',
        'domain_extension',
        'slug',
        'asking_price',
        'category',
        'description',
        'registration_date',
        'expiry_date',
        'has_website',
        'has_traffic',
        'premium_domain',
        'additional_features',
        'status',
        // New marketplace fields
        'bin_price',
        'accepts_offers',
        'minimum_offer',
        'commission_rate',
        'featured_listing',
        'featured_until',
        'domain_verified',
        'verification_method',
        'tags',
        'meta_title',
        'meta_description',
        'view_count',
        'favorite_count',
        'offer_count',
        'auto_renew',
        'renewal_price',
        // Bidding fields
        'enable_bidding',
        'starting_bid',
        'current_bid',
        'bid_count',
        'auction_start',
        'auction_end',
        'auction_status',
        'reserve_price',
        'reserve_met',
        'minimum_bid_increment',
        'auto_extend',
        'auto_extend_minutes',
        // Buy Now fields
        'enable_buy_now',
        'buy_now_price',
        'buy_now_available',
        'buy_now_expires_at',
        // Make An Offer fields
        'enable_offers',
        'maximum_offer',
        'auto_accept_offers',
        'auto_accept_threshold'
    ];


    protected $casts = [
        'asking_price' => 'decimal:2',
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'has_website' => 'boolean',
        'has_traffic' => 'boolean',
        'premium_domain' => 'boolean',
        // New marketplace field casts
        'bin_price' => 'decimal:2',
        'accepts_offers' => 'boolean',
        'minimum_offer' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'featured_listing' => 'boolean',
        'featured_until' => 'datetime',
        'domain_verified' => 'boolean',
        'tags' => 'array',
        'view_count' => 'integer',
        'favorite_count' => 'integer',
        'offer_count' => 'integer',
        'auto_renew' => 'boolean',
        'renewal_price' => 'decimal:2',
        // Bidding field casts
        'enable_bidding' => 'boolean',
        'starting_bid' => 'decimal:2',
        'current_bid' => 'decimal:2',
        'bid_count' => 'integer',
        'auction_start' => 'datetime',
        'auction_end' => 'datetime',
        'reserve_price' => 'decimal:2',
        'reserve_met' => 'boolean',
        'minimum_bid_increment' => 'integer',
        'auto_extend' => 'boolean',
        'auto_extend_minutes' => 'integer'
    ];

    /**
     * Get the user that owns the domain.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders for this domain.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the offers for this domain.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Get the active offers for this domain.
     */
    public function activeOffers(): HasMany
    {
        return $this->hasMany(Offer::class)->active();
    }

    /**
     * Get the messages related to this domain.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the favorites for this domain.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the bids for this domain.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the active bids for this domain.
     */
    public function activeBids(): HasMany
    {
        return $this->hasMany(Bid::class)->active();
    }

    /**
     * Get the highest bid for this domain.
     */
    public function highestBid(): HasMany
    {
        return $this->hasMany(Bid::class)->active()->orderBy('bid_amount', 'desc');
    }

    /**
     * Get conversations about this domain.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get watchlist entries for this domain.
     */
    public function watchlist(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get the current winning bid for this domain.
     */
    public function winningBid(): HasMany
    {
        return $this->hasMany(Bid::class)->winning();
    }

    /**
     * Get the full domain name (name + extension).
     */
    public function getFullDomainAttribute(): string
    {
        return $this->domain_name . $this->domain_extension;
    }

    /**
     * Get the formatted asking price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->asking_price, 2);
    }

    /**
     * Get the formatted BIN price.
     */
    public function getFormattedBinPriceAttribute(): string
    {
        if (!$this->bin_price) {
            return 'N/A';
        }
        return '$' . number_format($this->bin_price, 2);
    }

    /**
     * Get the formatted minimum offer.
     */
    public function getFormattedMinimumOfferAttribute(): string
    {
        if (!$this->minimum_offer) {
            return 'N/A';
        }
        return '$' . number_format($this->minimum_offer, 2);
    }

    /**
     * Get the formatted maximum offer.
     */
    public function getFormattedMaximumOfferAttribute(): string
    {
        if (!$this->maximum_offer) {
            return 'N/A';
        }
        return '$' . number_format($this->maximum_offer, 2);
    }

    /**
     * Get the formatted auto-accept threshold.
     */
    public function getFormattedAutoAcceptThresholdAttribute(): string
    {
        if (!$this->auto_accept_threshold) {
            return 'N/A';
        }
        return '$' . number_format($this->auto_accept_threshold, 2);
    }

    /**
     * Check if domain has BIN (Buy It Now) enabled.
     */
    public function hasBin(): bool
    {
        return !is_null($this->bin_price) && $this->bin_price > 0;
    }

    /**
     * Check if domain accepts offers.
     */
    public function acceptsOffers(): bool
    {
        return $this->enable_offers;
    }

    /**
     * Check if domain is featured.
     */
    public function isFeatured(): bool
    {
        if (!$this->featured_listing) {
            return false;
        }
        
        if (!$this->featured_until) {
            return true; // Featured indefinitely
        }
        
        return $this->featured_until->isFuture();
    }

    /**
     * Check if domain is verified.
     */
    public function isVerified(): bool
    {
        return $this->domain_verified;
    }

    /**
     * Check if domain is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }

    /**
     * Check if domain is expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->diffInDays(now()) <= 30;
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Get domain tags as array.
     */
    public function getTagsArrayAttribute(): array
    {
        return $this->tags ?? [];
    }

    /**
     * Get domain tags as comma-separated string.
     */
    public function getTagsStringAttribute(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Scope a query to only include active domains.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ==================== BIDDING METHODS ====================

    /**
     * Check if domain has bidding enabled.
     */
    public function hasBidding(): bool
    {
        return $this->enable_bidding;
    }

    /**
     * Check if domain is ready for bidding (has all required fields).
     */
    public function isReadyForBidding(): bool
    {
        return $this->enable_bidding && 
               $this->starting_bid && 
               $this->auction_start && 
               $this->auction_end;
    }

    /**
     * Check if domain is in auction mode.
     */
    public function isAuction(): bool
    {
        return $this->enable_bidding;
    }

    /**
     * Check if auction is currently active.
     */
    public function isAuctionActive(): bool
    {
        return $this->enable_bidding && $this->auction_status === 'active';
    }

    /**
     * Check if auction has ended.
     */
    public function isAuctionEnded(): bool
    {
        return $this->enable_bidding && $this->auction_status === 'ended';
    }

    /**
     * Check if auction is scheduled.
     */
    public function isAuctionScheduled(): bool
    {
        return $this->enable_bidding && $this->auction_status === 'scheduled';
    }

    /**
     * Get the formatted starting bid.
     */
    public function getFormattedStartingBidAttribute(): string
    {
        if (!$this->starting_bid) {
            return 'N/A';
        }
        return '$' . number_format($this->starting_bid, 2);
    }

    /**
     * Get the formatted current bid.
     */
    public function getFormattedCurrentBidAttribute(): string
    {
        if (!$this->current_bid) {
            return 'N/A';
        }
        return '$' . number_format($this->current_bid, 2);
    }

    /**
     * Get the formatted reserve price.
     */
    public function getFormattedReservePriceAttribute(): string
    {
        if (!$this->reserve_price) {
            return 'N/A';
        }
        return '$' . number_format($this->reserve_price, 2);
    }

    /**
     * Get the next minimum bid amount.
     */
    public function getNextMinimumBidAttribute(): float
    {
        if (!$this->current_bid) {
            return $this->starting_bid ?? 0;
        }
        return $this->current_bid + $this->minimum_bid_increment;
    }

    /**
     * Get the formatted next minimum bid.
     */
    public function getFormattedNextMinimumBidAttribute(): string
    {
        return '$' . number_format($this->getNextMinimumBidAttribute(), 2);
    }

    /**
     * Check if reserve price has been met.
     */
    public function isReserveMet(): bool
    {
        if (!$this->reserve_price) {
            return true; // No reserve set
        }
        return $this->current_bid >= $this->reserve_price;
    }

    /**
     * Get time remaining in auction.
     */
    public function getAuctionTimeRemainingAttribute(): string
    {
        if (!$this->auction_end || !$this->isAuctionActive()) {
            return 'N/A';
        }

        $now = now();
        $end = $this->auction_end;

        if ($now >= $end) {
            return 'Ended';
        }

        $diff = $now->diff($end);
        
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h ' . $diff->i . 'm';
        } elseif ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        } else {
            return $diff->i . 'm';
        }
    }

    /**
     * Check if auction is ending soon (within 1 hour).
     */
    public function isAuctionEndingSoon(): bool
    {
        if (!$this->auction_end || !$this->isAuctionActive()) {
            return false;
        }
        return now()->diffInMinutes($this->auction_end) <= 60;
    }

    /**
     * Get the winning bidder.
     */
    public function getWinningBidderAttribute()
    {
        if (!$this->isAuctionEnded()) {
            return null;
        }
        return $this->bids()->winning()->first()?->bidder;
    }

    /**
     * Scope to get only domains with active auctions.
     */
    public function scopeActiveAuctions($query)
    {
        return $query->where('enable_bidding', true)
                    ->where('auction_status', 'active');
    }

    /**
     * Scope to get only domains with scheduled auctions.
     */
    public function scopeScheduledAuctions($query)
    {
        return $query->where('enable_bidding', true)
                    ->where('auction_status', 'scheduled');
    }

    /**
     * Scope to get only domains with ended auctions.
     */
    public function scopeEndedAuctions($query)
    {
        return $query->where('enable_bidding', true)
                    ->where('auction_status', 'ended');
    }

    // ==================== BUY NOW METHODS ====================

    /**
     * Check if domain has buy now enabled.
     */
    public function hasBuyNow(): bool
    {
        return $this->enable_buy_now && $this->buy_now_available;
    }

    /**
     * Check if buy now is available (not expired).
     */
    public function isBuyNowAvailable(): bool
    {
        if (!$this->enable_buy_now || !$this->buy_now_available) {
            return false;
        }

        if ($this->buy_now_expires_at && now()->isAfter($this->buy_now_expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get the formatted buy now price.
     */
    public function getFormattedBuyNowPriceAttribute(): string
    {
        if (!$this->buy_now_price) {
            return 'N/A';
        }
        return '$' . number_format($this->buy_now_price, 2);
    }

    /**
     * Disable buy now (when purchased).
     */
    public function disableBuyNow(): void
    {
        $this->update(['buy_now_available' => false]);
    }

    // ==================== OFFER METHODS ====================

    /**
     * Check if an offer amount is valid.
     */
    public function isValidOfferAmount($amount): bool
    {
        if (!$this->acceptsOffers()) {
            return false;
        }

        if ($this->minimum_offer && $amount < $this->minimum_offer) {
            return false;
        }

        if ($this->maximum_offer && $amount > $this->maximum_offer) {
            return false;
        }

        return true;
    }

    /**
     * Check if an offer should be auto-accepted.
     */
    public function shouldAutoAcceptOffer($amount): bool
    {
        if (!$this->auto_accept_offers || !$this->auto_accept_threshold) {
            return false;
        }

        return $amount >= $this->auto_accept_threshold;
    }



    /**
     * Scope a query to only include draft domains.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include sold domains.
     */
    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    /**
     * Scope a query to only include featured domains.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured_listing', true)
                    ->where(function ($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope a query to only include verified domains.
     */
    public function scopeVerified($query)
    {
        return $query->where('domain_verified', true);
    }

    /**
     * Scope a query to only include domains with BIN.
     */
    public function scopeWithBin($query)
    {
        return $query->whereNotNull('bin_price')
                    ->where('bin_price', '>', 0);
    }

    /**
     * Scope a query to only include domains that accept offers.
     */
    public function scopeAcceptsOffers($query)
    {
        return $query->where('accepts_offers', true);
    }

    /**
     * Scope a query to only include domains in specific price range.
     */
    public function scopeInPriceRange($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('asking_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope a query to only include domains with specific extension.
     */
    public function scopeWithExtension($query, string $extension)
    {
        return $query->where('domain_extension', $extension);
    }

    /**
     * Scope a query to only include domains in specific category.
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include domains with specific tags.
     */
    public function scopeWithTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }

    /**
     * Search domains by keyword.
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('domain_name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('category', 'like', "%{$keyword}%")
              ->orWhereJsonContains('tags', $keyword);
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Generate a unique slug for the domain.
     */
    public function generateSlug()
    {
        $baseSlug = strtolower($this->full_domain);
        $slug = preg_replace('/[^a-z0-9-]/', '-', $baseSlug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Check if domain has any pending actions that prevent status change.
     */
    public function hasPendingActions(): bool
    {
        return $this->hasActiveBids() || 
               $this->hasPendingOffers() || 
               $this->hasPendingOrders() ||
               $this->hasActiveAuction();
    }

    /**
     * Check if domain has active bids.
     */
    public function hasActiveBids(): bool
    {
        return $this->bids()->where('status', 'active')->exists();
    }

    /**
     * Check if domain has pending offers.
     */
    public function hasPendingOffers(): bool
    {
        return $this->offers()->where('status', 'pending')->exists();
    }

    /**
     * Check if domain has pending orders.
     */
    public function hasPendingOrders(): bool
    {
        return $this->orders()->whereIn('status', ['pending', 'paid', 'in_escrow'])->exists();
    }

    /**
     * Check if domain has an active auction.
     */
    public function hasActiveAuction(): bool
    {
        return $this->enable_bidding && 
               $this->auction_status === 'active' && 
               $this->auction_end && 
               $this->auction_end->isFuture();
    }

    /**
     * Get pending actions summary for display.
     */
    public function getPendingActionsSummary(): array
    {
        $actions = [];
        
        if ($this->hasActiveBids()) {
            $bidCount = $this->bids()->where('status', 'active')->count();
            $actions[] = "{$bidCount} active bid" . ($bidCount > 1 ? 's' : '');
        }
        
        if ($this->hasPendingOffers()) {
            $offerCount = $this->offers()->where('status', 'pending')->count();
            $actions[] = "{$offerCount} pending offer" . ($offerCount > 1 ? 's' : '');
        }
        
        if ($this->hasPendingOrders()) {
            $orderCount = $this->orders()->whereIn('status', ['pending', 'paid', 'in_escrow'])->count();
            $actions[] = "{$orderCount} pending order" . ($orderCount > 1 ? 's' : '');
        }
        
        if ($this->hasActiveAuction()) {
            $actions[] = "active auction";
        }
        
        return $actions;
    }

    /**
     * Boot method to automatically generate slug before saving.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($domain) {
            if (empty($domain->slug)) {
                $domain->slug = $domain->generateSlug();
            }
        });
        
        static::updating(function ($domain) {
            if ($domain->isDirty('domain_name') || $domain->isDirty('domain_extension')) {
                $domain->slug = $domain->generateSlug();
            }
            
            // Prevent setting status to 'active' without domain verification
            if ($domain->isDirty('status') && $domain->status === 'active' && !$domain->domain_verified) {
                throw new \Exception('Domain must be verified before it can be published.');
            }
        });
    }
}
