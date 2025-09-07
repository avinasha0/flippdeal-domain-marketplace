<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AmlFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'flag_type',
        'description',
        'metadata',
        'status',
        'reviewed_by_admin_id',
        'reviewed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the AML flag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed the AML flag.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by flag type
     */
    public function scopeFlagType($query, $flagType)
    {
        return $query->where('flag_type', $flagType);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter active flags
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter resolved flags
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to filter false positive flags
     */
    public function scopeFalsePositive($query)
    {
        return $query->where('status', 'false_positive');
    }

    /**
     * Check if the flag is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the flag is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if the flag is false positive
     */
    public function isFalsePositive(): bool
    {
        return $this->status === 'false_positive';
    }

    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'red',
            'resolved' => 'green',
            'false_positive' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'resolved' => 'Resolved',
            'false_positive' => 'False Positive',
            default => 'Unknown',
        };
    }

    /**
     * Get the flag type label
     */
    public function getFlagTypeLabelAttribute(): string
    {
        return match ($this->flag_type) {
            'high_volume' => 'High Volume Transactions',
            'rapid_transfers' => 'Rapid Transfers',
            'email_mismatch' => 'Email Mismatch',
            'multiple_high_value' => 'Multiple High-Value Transactions',
            default => ucwords(str_replace('_', ' ', $this->flag_type)),
        };
    }
}
