<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DomainTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'transaction_id',
        'from_user_id',
        'to_user_id',
        'transfer_method',
        'evidence_data',
        'evidence_url',
        'transfer_notes',
        'verified',
        'verified_by_admin_id',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'evidence_data' => 'array',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Transfer methods
    const METHOD_REGISTRAR = 'registrar';
    const METHOD_DNS = 'dns';
    const METHOD_MANUAL = 'manual';
    const METHOD_AUTH_CODE = 'auth_code';

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the transaction.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user who initiated the transfer (seller).
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user receiving the transfer (buyer).
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the admin who verified the transfer.
     */
    public function verifiedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    /**
     * Get audit logs for this transfer.
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    /**
     * Check if transfer is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * Check if transfer is pending verification.
     */
    public function isPendingVerification(): bool
    {
        return !$this->verified;
    }

    /**
     * Get transfer method display name.
     */
    public function getTransferMethodDisplayAttribute(): string
    {
        return match ($this->transfer_method) {
            self::METHOD_REGISTRAR => 'Registrar Transfer',
            self::METHOD_DNS => 'DNS Change',
            self::METHOD_MANUAL => 'Manual Transfer',
            self::METHOD_AUTH_CODE => 'Auth Code Transfer',
            default => 'Unknown Method',
        };
    }

    /**
     * Get verification status badge class.
     */
    public function getVerificationBadgeClassAttribute(): string
    {
        return $this->verified 
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    }

    /**
     * Get verification status text.
     */
    public function getVerificationStatusTextAttribute(): string
    {
        return $this->verified ? 'Verified' : 'Pending Verification';
    }

    /**
     * Boot method to log verification changes.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($transfer) {
            if ($transfer->isDirty('verified')) {
                $oldVerified = $transfer->getOriginal('verified');
                $newVerified = $transfer->verified;
                
                // Log the verification change
                $transfer->audits()->create([
                    'event' => 'verification_changed',
                    'old_values' => ['verified' => $oldVerified],
                    'new_values' => ['verified' => $newVerified],
                    'user_id' => auth()->id(),
                    'user_type' => auth()->user()?->isAdmin() ? 'admin' : 'user',
                    'description' => $newVerified ? 'Transfer verified' : 'Transfer verification removed',
                ]);
            }
        });
    }
}