<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id',
        'action',
        'target_type',
        'target_id',
        'payload',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Get the user who performed the action.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Get the target model.
     */
    public function target(): MorphTo
    {
        return $this->morphTo('target', 'target_type', 'target_id');
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', 'like', "%{$action}%");
    }

    /**
     * Scope to filter by actor
     */
    public function scopeActor($query, $actorId)
    {
        return $query->where('actor_id', $actorId);
    }

    /**
     * Scope to filter by target type
     */
    public function scopeTargetType($query, $targetType)
    {
        return $query->where('target_type', $targetType);
    }

    /**
     * Scope to filter by target ID
     */
    public function scopeTargetId($query, $targetId)
    {
        return $query->where('target_id', $targetId);
    }

    /**
     * Scope to filter by IP
     */
    public function scopeIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by recent activity
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Get the action category
     */
    public function getActionCategoryAttribute(): string
    {
        $parts = explode('.', $this->action);
        return $parts[0] ?? 'unknown';
    }

    /**
     * Get the action type
     */
    public function getActionTypeAttribute(): string
    {
        $parts = explode('.', $this->action);
        return $parts[1] ?? 'unknown';
    }

    /**
     * Get the action description
     */
    public function getActionDescriptionAttribute(): string
    {
        return match ($this->action_category) {
            'domain' => $this->getDomainActionDescription(),
            'transaction' => $this->getTransactionActionDescription(),
            'admin' => $this->getAdminActionDescription(),
            'security' => $this->getSecurityActionDescription(),
            'file' => $this->getFileActionDescription(),
            'rate_limit' => $this->getRateLimitActionDescription(),
            'bot_detection' => $this->getBotDetectionActionDescription(),
            default => ucwords(str_replace('.', ' ', $this->action)),
        };
    }

    /**
     * Get domain action description
     */
    protected function getDomainActionDescription(): string
    {
        return match ($this->action_type) {
            'verification' => match (explode('.', $this->action)[2] ?? '') {
                'created' => 'Domain verification created',
                'verified' => 'Domain verification completed',
                'needs_admin' => 'Domain verification needs admin review',
                'admin_approved' => 'Domain verification approved by admin',
                'admin_rejected' => 'Domain verification rejected by admin',
                default => 'Domain verification action',
            },
            'status' => 'Domain status changed',
            default => 'Domain action',
        };
    }

    /**
     * Get transaction action description
     */
    protected function getTransactionActionDescription(): string
    {
        return match ($this->action_type) {
            'created' => 'Transaction created',
            'paid' => 'Transaction paid',
            'released' => 'Escrow released',
            'refunded' => 'Transaction refunded',
            'cancelled' => 'Transaction cancelled',
            default => 'Transaction action',
        };
    }

    /**
     * Get admin action description
     */
    protected function getAdminActionDescription(): string
    {
        return match ($this->action_type) {
            'login' => 'Admin login',
            'logout' => 'Admin logout',
            'approve' => 'Admin approval',
            'reject' => 'Admin rejection',
            'override' => 'Admin override',
            default => 'Admin action',
        };
    }

    /**
     * Get security action description
     */
    protected function getSecurityActionDescription(): string
    {
        return match ($this->action_type) {
            'login_failed' => 'Failed login attempt',
            'recaptcha_failed' => 'ReCAPTCHA verification failed',
            'suspicious_activity' => 'Suspicious activity detected',
            'rate_limit_exceeded' => 'Rate limit exceeded',
            default => 'Security event',
        };
    }

    /**
     * Get file action description
     */
    protected function getFileActionDescription(): string
    {
        return match ($this->action_type) {
            'uploaded' => 'File uploaded',
            'scanned' => 'File scanned',
            'quarantined' => 'File quarantined',
            'deleted' => 'File deleted',
            default => 'File action',
        };
    }

    /**
     * Get rate limit action description
     */
    protected function getRateLimitActionDescription(): string
    {
        $endpoint = explode('.', $this->action)[1] ?? 'unknown';
        return "Rate limit exceeded for {$endpoint}";
    }

    /**
     * Get bot detection action description
     */
    protected function getBotDetectionActionDescription(): string
    {
        $reason = explode('.', $this->action)[1] ?? 'unknown';
        return "Bot detection: {$reason}";
    }

    /**
     * Get formatted payload for display
     */
    public function getFormattedPayloadAttribute(): array
    {
        $payload = $this->payload ?? [];
        
        // Remove sensitive fields for display
        $sensitiveFields = ['password', 'token', 'secret', 'api_key'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($payload[$field])) {
                $payload[$field] = '[MASKED]';
            }
        }
        
        return $payload;
    }

    /**
     * Get time ago string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get severity level
     */
    public function getSeverityLevelAttribute(): string
    {
        if (str_contains($this->action, 'security') || str_contains($this->action, 'bot_detection')) {
            return 'high';
        }
        
        if (str_contains($this->action, 'failed') || str_contains($this->action, 'error')) {
            return 'medium';
        }
        
        return 'low';
    }

    /**
     * Get severity color
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity_level) {
            'high' => 'red',
            'medium' => 'orange',
            'low' => 'green',
            default => 'gray',
        };
    }
}