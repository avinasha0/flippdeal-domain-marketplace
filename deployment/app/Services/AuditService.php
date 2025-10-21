<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Log an audit entry
     */
    public function log(?User $actor, string $action, ?Model $target = null, array $payload = []): Audit
    {
        try {
            // Mask sensitive data in payload
            $maskedPayload = $this->maskSensitiveData($payload);

            $audit = Audit::create([
                'actor_id' => $actor?->id,
                'action' => $action,
                'target_type' => $target ? get_class($target) : null,
                'target_id' => $target?->id,
                'payload' => $maskedPayload,
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);

            Log::info('Audit log created', [
                'audit_id' => $audit->id,
                'action' => $action,
                'actor_id' => $actor?->id,
                'target_type' => $target ? get_class($target) : null,
                'target_id' => $target?->id,
            ]);

            return $audit;

        } catch (\Exception $e) {
            Log::error('Failed to create audit log', [
                'action' => $action,
                'actor_id' => $actor?->id,
                'target_type' => $target ? get_class($target) : null,
                'target_id' => $target?->id,
                'error' => $e->getMessage(),
            ]);

            // Return a minimal audit entry even if creation fails
            return new Audit([
                'actor_id' => $actor?->id,
                'action' => $action,
                'target_type' => $target ? get_class($target) : null,
                'target_id' => $target?->id,
                'payload' => ['error' => 'Audit creation failed'],
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }

    /**
     * Log domain verification events
     */
    public function logDomainVerification(User $actor, Domain $domain, string $action, array $payload = []): Audit
    {
        return $this->log($actor, "domain.verification.{$action}", $domain, $payload);
    }

    /**
     * Log domain status changes
     */
    public function logDomainStatusChange(User $actor, Domain $domain, string $oldStatus, string $newStatus, array $payload = []): Audit
    {
        return $this->log($actor, 'domain.status.changed', $domain, array_merge($payload, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]));
    }

    /**
     * Log transaction events
     */
    public function logTransactionEvent(User $actor, $transaction, string $action, array $payload = []): Audit
    {
        return $this->log($actor, "transaction.{$action}", $transaction, $payload);
    }

    /**
     * Log admin actions
     */
    public function logAdminAction(User $admin, string $action, ?Model $target = null, array $payload = []): Audit
    {
        return $this->log($admin, "admin.{$action}", $target, $payload);
    }

    /**
     * Log security events
     */
    public function logSecurityEvent(?User $actor, string $action, array $payload = []): Audit
    {
        return $this->log($actor, "security.{$action}", null, $payload);
    }

    /**
     * Log file upload events
     */
    public function logFileUpload(User $actor, string $action, array $payload = []): Audit
    {
        return $this->log($actor, "file.upload.{$action}", null, $payload);
    }

    /**
     * Log rate limiting events
     */
    public function logRateLimit(User $actor, string $endpoint, array $payload = []): Audit
    {
        return $this->log($actor, "rate_limit.{$endpoint}", null, $payload);
    }

    /**
     * Log bot detection events
     */
    public function logBotDetection(User $actor, string $reason, array $payload = []): Audit
    {
        return $this->log($actor, "bot_detection.{$reason}", null, $payload);
    }

    /**
     * Mask sensitive data in payload
     */
    protected function maskSensitiveData(array $payload): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'api_key',
            'secret',
            'token',
            'credit_card',
            'cvv',
            'ssn',
            'social_security',
            'bank_account',
            'routing_number',
            'paypal_email',
            'government_id',
        ];

        $masked = $payload;

        foreach ($sensitiveFields as $field) {
            if (isset($masked[$field])) {
                $masked[$field] = $this->maskValue($masked[$field]);
            }
        }

        // Recursively mask nested arrays
        foreach ($masked as $key => $value) {
            if (is_array($value)) {
                $masked[$key] = $this->maskSensitiveData($value);
            }
        }

        return $masked;
    }

    /**
     * Mask a sensitive value
     */
    protected function maskValue($value): string
    {
        if (is_string($value)) {
            $length = strlen($value);
            if ($length <= 4) {
                return str_repeat('*', $length);
            }
            return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
        }

        return '[MASKED]';
    }

    /**
     * Get audit logs with filters
     */
    public function getAuditLogs(array $filters = [], int $perPage = 20)
    {
        $query = Audit::with(['actor']);

        // Filter by action
        if (isset($filters['action'])) {
            $query->where('action', 'like', "%{$filters['action']}%");
        }

        // Filter by actor
        if (isset($filters['actor_id'])) {
            $query->where('actor_id', $filters['actor_id']);
        }

        // Filter by target
        if (isset($filters['target_type'])) {
            $query->where('target_type', $filters['target_type']);
        }

        if (isset($filters['target_id'])) {
            $query->where('target_id', $filters['target_id']);
        }

        // Filter by date range
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Filter by IP
        if (isset($filters['ip'])) {
            $query->where('ip', $filters['ip']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get audit statistics
     */
    public function getAuditStatistics(array $filters = []): array
    {
        $query = Audit::query();

        // Apply same filters as getAuditLogs
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $total = $query->count();

        $byAction = $query->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        $byActor = $query->selectRaw('actor_id, COUNT(*) as count')
            ->whereNotNull('actor_id')
            ->groupBy('actor_id')
            ->orderBy('count', 'desc')
            ->with('actor')
            ->get();

        $byIp = $query->selectRaw('ip, COUNT(*) as count')
            ->whereNotNull('ip')
            ->groupBy('ip')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'total' => $total,
            'by_action' => $byAction,
            'by_actor' => $byActor,
            'by_ip' => $byIp,
        ];
    }
}
