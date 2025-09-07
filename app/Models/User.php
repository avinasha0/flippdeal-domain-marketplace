<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'bio',
        'avatar',
        'is_verified',
        'last_login_at',
        'settings',
        // Verification fields
        'paypal_email',
        'paypal_verified',
        'paypal_verified_at',
        'government_id_path',
        'government_id_verified',
        'government_id_verified_at',
        'government_id_rejection_reason',
        // Two-factor authentication
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        // Account status
        'account_status',
        'suspended_at',
        'suspension_reason',
        // Additional profile fields
        'company_name',
        'website',
        'location',
        'social_links'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'last_login_at' => 'datetime',
            'settings' => 'array',
            // Verification field casts
            'paypal_verified' => 'boolean',
            'paypal_verified_at' => 'datetime',
            'government_id_verified' => 'boolean',
            'government_id_verified_at' => 'datetime',
            // Two-factor authentication casts
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            // Account status casts
            'suspended_at' => 'datetime',
            // Additional profile field casts
            'social_links' => 'array'
        ];
    }

    /**
     * Get the role assigned to this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }

    /**
     * Get the domains owned by the user.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get the orders where user is the buyer.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get the orders where user is the seller.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /**
     * Get all orders for the user (both buyer and seller).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    /**
     * Get the offers made by the user.
     */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'buyer_id');
    }

    /**
     * Get the offers received for user's domains.
     */
    public function receivedOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'seller_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the user's favorite domains.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the payment transactions for the user.
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the verifications for the user.
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get conversations where user is the buyer.
     */
    public function buyerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    /**
     * Get conversations where user is the seller.
     */
    public function sellerConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    /**
     * Get all conversations for the user.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id')
            ->orWhere('seller_id', $this->id);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's watchlist.
     */
    public function watchlist(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get the user's bids.
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get transactions where this user is the buyer.
     */
    public function buyerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Get transactions where this user is the seller.
     */
    public function sellerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * Get domain transfers initiated by this user.
     */
    public function initiatedTransfers(): HasMany
    {
        return $this->hasMany(DomainTransfer::class, 'from_user_id');
    }

    /**
     * Get domain transfers received by this user.
     */
    public function receivedTransfers(): HasMany
    {
        return $this->hasMany(DomainTransfer::class, 'to_user_id');
    }

    /**
     * Get domain transfers verified by this admin.
     */
    public function verifiedTransfers(): HasMany
    {
        return $this->hasMany(DomainTransfer::class, 'verified_by_admin_id');
    }

    /**
     * Get transactions released by this admin.
     */
    public function releasedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'escrow_release_by_admin_id');
    }


    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermission($permission);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasAnyPermission($permissions);
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasAllPermissions($permissions);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }

    /**
     * Check if user is a moderator.
     */
    public function isModerator(): bool
    {
        return $this->role && $this->role->name === 'moderator';
    }

    /**
     * Check if user is verified.
     */
    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    /**
     * Check if user has completed basic verification (either PayPal or government ID).
     */
    public function hasCompletedBasicVerification(): bool
    {
        return $this->isPayPalVerified() || $this->isGovernmentIdVerified();
    }

    /**
     * Get user's display name or fallback to email.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get user's avatar URL or fallback to default.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->display_name) . '&color=7C3AED&background=EBF4FF';
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Get unread message count.
     */
    public function getUnreadMessageCountAttribute(): int
    {
        return $this->getUnreadConversationCountAttribute();
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadNotificationCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get unread conversation count.
     */
    public function getUnreadConversationCountAttribute(): int
    {
        $buyerUnread = $this->buyerConversations()->sum('buyer_unread_count');
        $sellerUnread = $this->sellerConversations()->sum('seller_unread_count');
        return $buyerUnread + $sellerUnread;
    }

    /**
     * Get the KYC requests for the user.
     */
    public function kycRequests(): HasMany
    {
        return $this->hasMany(KycRequest::class);
    }

    /**
     * Get the AML flags for the user.
     */
    public function amlFlags(): HasMany
    {
        return $this->hasMany(AmlFlag::class);
    }

    /**
     * Check if user is watching a domain.
     */
    public function isWatching(int $domainId): bool
    {
        return $this->watchlist()->where('domain_id', $domainId)->exists();
    }

    /**
     * Get watchlist count.
     */
    public function getWatchlistCountAttribute(): int
    {
        return $this->watchlist()->count();
    }

    /**
     * Get total sales amount.
     */
    public function getTotalSalesAttribute(): float
    {
        return $this->sales()->completed()->sum('seller_amount');
    }

    /**
     * Get total purchases amount.
     */
    public function getTotalPurchasesAttribute(): float
    {
        return $this->purchases()->completed()->sum('total_amount');
    }

    // ==================== VERIFICATION METHODS ====================

    /**
     * Check if user's PayPal email is verified.
     */
    public function isPayPalVerified(): bool
    {
        return (bool) $this->paypal_verified;
    }

    /**
     * Check if user's government ID is verified.
     */
    public function isGovernmentIdVerified(): bool
    {
        return (bool) $this->government_id_verified;
    }

    /**
     * Check if user is fully verified (both PayPal and government ID).
     */
    public function isFullyVerified(): bool
    {
        return $this->isPayPalVerified() && $this->isGovernmentIdVerified();
    }

    /**
     * Check if user account is active.
     */
    public function isAccountActive(): bool
    {
        return $this->account_status === 'active';
    }

    /**
     * Check if user account is suspended.
     */
    public function isAccountSuspended(): bool
    {
        return $this->account_status === 'suspended';
    }

    /**
     * Check if user account is pending verification.
     */
    public function isAccountPendingVerification(): bool
    {
        return $this->account_status === 'pending_verification';
    }

    /**
     * Check if user has two-factor authentication enabled.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled;
    }

    /**
     * Get pending verifications for the user.
     */
    public function getPendingVerifications()
    {
        return $this->verifications()->pending()->get();
    }

    /**
     * Get verification by type.
     */
    public function getVerificationByType(string $type)
    {
        return $this->verifications()->ofType($type)->latest()->first();
    }


    /**
     * Mark PayPal email as verified.
     */
    public function markPayPalAsVerified(): void
    {
        $this->update([
            'paypal_verified' => true,
            'paypal_verified_at' => now()
        ]);
    }

    /**
     * Mark government ID as verified.
     */
    public function markGovernmentIdAsVerified(): void
    {
        $this->update([
            'government_id_verified' => true,
            'government_id_verified_at' => now(),
            'government_id_rejection_reason' => null
        ]);
    }

    /**
     * Reject government ID verification.
     */
    public function rejectGovernmentIdVerification(string $reason): void
    {
        $this->update([
            'government_id_verified' => false,
            'government_id_rejection_reason' => $reason
        ]);
    }

    /**
     * Suspend user account.
     */
    public function suspend(string $reason): void
    {
        $this->update([
            'account_status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $reason
        ]);
    }

    /**
     * Activate user account.
     */
    public function activate(): void
    {
        $this->update([
            'account_status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null
        ]);
    }

    /**
     * Get user's social links as array.
     */
    public function getSocialLinksArrayAttribute(): array
    {
        return $this->social_links ?? [];
    }

    /**
     * Get user's government ID URL.
     */
    public function getGovernmentIdUrlAttribute(): ?string
    {
        if (!$this->government_id_path) {
            return null;
        }
        return asset('storage/' . $this->government_id_path);
    }
}
