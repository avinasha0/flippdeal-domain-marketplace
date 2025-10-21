<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DomainVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'method',
        'token',
        'token_expires_at',
        'status',
        'evidence',
        'raw_whois',
        'attempts',
        'last_checked_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'evidence' => 'array',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Get the domain that owns the verification.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by method
     */
    public function scopeMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope to filter pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter verified verifications
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope to filter verifications needing admin review
     */
    public function scopeNeedsAdmin($query)
    {
        return $query->where('status', 'needs_admin');
    }

    /**
     * Scope to filter failed verifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter expired verifications
     */
    public function scopeExpired($query)
    {
        return $query->where('token_expires_at', '<', now());
    }

    /**
     * Check if verification is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if verification is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if verification needs admin review
     */
    public function needsAdmin(): bool
    {
        return $this->status === 'needs_admin';
    }

    /**
     * Check if verification is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if verification is expired
     */
    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at < now();
    }

    /**
     * Check if verification can be retried
     */
    public function canRetry(): bool
    {
        return $this->isPending() && 
               !$this->isExpired() && 
               $this->attempts < config('verification.max_attempts', 12);
    }

    /**
     * Get the status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'verified' => 'green',
            'failed' => 'red',
            'needs_admin' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'failed' => 'Failed',
            'needs_admin' => 'Needs Admin Review',
            default => 'Unknown',
        };
    }

    /**
     * Get the method label
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'dns_txt' => 'DNS TXT Record',
            'dns_cname' => 'DNS CNAME Record',
            'file_upload' => 'File Upload',
            'whois' => 'WHOIS Data',
            default => ucwords(str_replace('_', ' ', $this->method)),
        };
    }

    /**
     * Get time remaining until expiration
     */
    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->token_expires_at) {
            return null;
        }

        $remaining = $this->token_expires_at->diffInSeconds(now());
        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Get formatted time remaining
     */
    public function getFormattedTimeRemainingAttribute(): ?string
    {
        $remaining = $this->time_remaining;
        
        if ($remaining === null) {
            return null;
        }

        if ($remaining <= 0) {
            return 'Expired';
        }

        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        $seconds = $remaining % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m {$seconds}s";
        } elseif ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        } else {
            return "{$seconds}s";
        }
    }

    /**
     * Get DNS instructions for TXT verification
     */
    public function getDnsInstructionsAttribute(): ?array
    {
        if ($this->method !== 'dns_txt' || !$this->token) {
            return null;
        }

        return [
            'record_type' => 'TXT',
            'record_name' => $this->domain->full_domain,
            'record_value' => $this->token,
            'ttl_recommendation' => 300,
        ];
    }

    /**
     * Get verification progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->isVerified()) {
            return 100;
        }

        if ($this->isFailed() || $this->isExpired()) {
            return 0;
        }

        $maxAttempts = config('verification.max_attempts', 12);
        $progress = ($this->attempts / $maxAttempts) * 100;
        
        return min(100, max(0, (int) $progress));
    }
}
