<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\DomainVerification;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DomainVerificationService
{
    protected $auditService;
    protected $dnsResolver;
    protected $tokenTtl;
    protected $maxAttempts;

    public function __construct(AuditService $auditService, DnsResolverInterface $dnsResolver)
    {
        $this->auditService = $auditService;
        $this->dnsResolver = $dnsResolver;
        $this->tokenTtl = config('verification.token_ttl_minutes', 120);
        $this->maxAttempts = config('verification.max_attempts', 12);
    }

    /**
     * Create DNS TXT verification for a domain
     */
    public function createDnsVerification(Domain $domain, User $user): array
    {
        // Check if there's already a pending verification
        $existing = DomainVerification::where('domain_id', $domain->id)
            ->where('status', 'pending')
            ->where('method', 'dns_txt')
            ->first();

        if ($existing && $existing->token_expires_at > now()) {
            return [
                'success' => true,
                'verification' => $existing,
                'instructions' => $this->generateDnsInstructions($existing->token, $domain->full_domain),
            ];
        }

        // Generate cryptographically secure token
        $token = hash('sha256', random_bytes(32));
        $expiresAt = now()->addMinutes($this->tokenTtl);

        $verification = DomainVerification::create([
            'domain_id' => $domain->id,
            'method' => 'dns_txt',
            'token' => $token,
            'token_expires_at' => $expiresAt,
            'status' => 'pending',
            'evidence' => [
                'created_by' => $user->id,
                'created_at' => now()->toISOString(),
                'ttl_recommendation' => 300, // 5 minutes
            ],
        ]);

        // Log the verification creation
        $this->auditService->log($user, 'domain.verification.created', $domain, [
            'method' => 'dns_txt',
            'token_expires_at' => $expiresAt->toISOString(),
        ]);

        return [
            'success' => true,
            'verification' => $verification,
            'instructions' => $this->generateDnsInstructions($token, $domain->full_domain),
        ];
    }

    /**
     * Check DNS verification status
     */
    public function checkDnsVerification(DomainVerification $verification): array
    {
        if ($verification->status !== 'pending' || $verification->method !== 'dns_txt') {
            return ['success' => false, 'message' => 'Invalid verification state'];
        }

        if ($verification->token_expires_at < now()) {
            $verification->update(['status' => 'failed']);
            return ['success' => false, 'message' => 'Token expired'];
        }

        if ($verification->attempts >= $this->maxAttempts) {
            $this->markNeedsAdmin($verification, 'Max attempts reached');
            return ['success' => false, 'message' => 'Max attempts reached'];
        }

        try {
            $domain = $verification->domain;
            $dnsRecords = $this->dnsResolver->getTxtRecords($domain->full_domain);
            
            $verification->increment('attempts');
            $verification->update(['last_checked_at' => now()]);

            $expectedToken = $verification->token;
            $found = false;

            foreach ($dnsRecords as $record) {
                if (trim($record) === $expectedToken) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $this->markVerified($verification, [
                    'verified_at' => now()->toISOString(),
                    'dns_records_found' => $dnsRecords,
                ]);
                return ['success' => true, 'verified' => true];
            }

            // Check if we've reached max attempts
            if ($verification->attempts >= $this->maxAttempts) {
                $this->markNeedsAdmin($verification, 'Token not found after max attempts');
                return ['success' => false, 'message' => 'Token not found after max attempts'];
            }

            return ['success' => true, 'verified' => false, 'attempts' => $verification->attempts];

        } catch (\Exception $e) {
            Log::error('DNS verification check failed', [
                'verification_id' => $verification->id,
                'domain' => $verification->domain->full_domain,
                'error' => $e->getMessage(),
            ]);

            $verification->increment('attempts');
            
            if ($verification->attempts >= $this->maxAttempts) {
                $this->markNeedsAdmin($verification, 'DNS lookup failed repeatedly: ' . $e->getMessage());
            }

            return ['success' => false, 'message' => 'DNS lookup failed'];
        }
    }

    /**
     * Mark verification as verified
     */
    public function markVerified(DomainVerification $verification, array $evidence = []): void
    {
        $verification->update([
            'status' => 'verified',
            'evidence' => array_merge($verification->evidence ?? [], $evidence),
        ]);

        // Update domain verification status
        $verification->domain->update(['domain_verified' => true]);

        // Log the verification success
        $this->auditService->log(
            $verification->domain->user,
            'domain.verification.verified',
            $verification->domain,
            array_merge($evidence, ['method' => $verification->method])
        );
    }

    /**
     * Mark verification as needing admin review
     */
    public function markNeedsAdmin(DomainVerification $verification, string $reason): void
    {
        $verification->update([
            'status' => 'needs_admin',
            'evidence' => array_merge($verification->evidence ?? [], [
                'needs_admin_reason' => $reason,
                'needs_admin_at' => now()->toISOString(),
            ]),
        ]);

        // Log the admin review requirement
        $this->auditService->log(
            $verification->domain->user,
            'domain.verification.needs_admin',
            $verification->domain,
            ['reason' => $reason, 'method' => $verification->method]
        );

        // Notify admin (this would dispatch a notification job)
        // NotificationService::notifyAdmins('verification_needs_review', $verification);
    }

    /**
     * Admin approval of verification
     */
    public function adminApprove(DomainVerification $verification, User $admin, string $notes = null): void
    {
        $verification->update([
            'status' => 'verified',
            'evidence' => array_merge($verification->evidence ?? [], [
                'admin_approved' => true,
                'approved_by' => $admin->id,
                'approved_at' => now()->toISOString(),
                'admin_notes' => $notes,
            ]),
        ]);

        // Update domain verification status
        $verification->domain->update(['domain_verified' => true]);

        // Log the admin approval
        $this->auditService->log($admin, 'domain.verification.admin_approved', $verification->domain, [
            'verification_id' => $verification->id,
            'method' => $verification->method,
            'notes' => $notes,
        ]);
    }

    /**
     * Admin rejection of verification
     */
    public function adminReject(DomainVerification $verification, User $admin, string $reason): void
    {
        $verification->update([
            'status' => 'failed',
            'evidence' => array_merge($verification->evidence ?? [], [
                'admin_rejected' => true,
                'rejected_by' => $admin->id,
                'rejected_at' => now()->toISOString(),
                'rejection_reason' => $reason,
            ]),
        ]);

        // Log the admin rejection
        $this->auditService->log($admin, 'domain.verification.admin_rejected', $verification->domain, [
            'verification_id' => $verification->id,
            'method' => $verification->method,
            'reason' => $reason,
        ]);
    }

    /**
     * Check WHOIS verification
     */
    public function checkWhoisVerification(Domain $domain, User $user): array
    {
        try {
            $whoisData = $this->dnsResolver->getWhoisData($domain->full_domain);
            
            if (!$whoisData) {
                return ['success' => false, 'message' => 'WHOIS data not available'];
            }

            $evidence = [
                'whois_data' => $whoisData,
                'checked_at' => now()->toISOString(),
                'flags' => $this->checkWhoisFlags($domain, $user, $whoisData),
            ];

            $verification = DomainVerification::create([
                'domain_id' => $domain->id,
                'method' => 'whois',
                'status' => 'verified',
                'evidence' => $evidence,
                'raw_whois' => json_encode($whoisData),
            ]);

            // Update domain verification status
            $domain->update(['domain_verified' => true]);

            // Log the verification
            $this->auditService->log($user, 'domain.verification.verified', $domain, [
                'method' => 'whois',
                'flags' => $evidence['flags'],
            ]);

            return ['success' => true, 'verification' => $verification, 'evidence' => $evidence];

        } catch (\Exception $e) {
            Log::error('WHOIS verification failed', [
                'domain_id' => $domain->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'WHOIS verification failed'];
        }
    }

    /**
     * Check for WHOIS flags and mismatches
     */
    protected function checkWhoisFlags(Domain $domain, User $user, array $whoisData): array
    {
        $flags = [];

        // Check email mismatch
        if (isset($whoisData['email']) && $user->email !== $whoisData['email']) {
            $flags[] = 'email_mismatch';
        }

        // Check PayPal email mismatch
        if (isset($whoisData['email']) && $user->paypal_email && $user->paypal_email !== $whoisData['email']) {
            $flags[] = 'paypal_email_mismatch';
        }

        // Check registrant name mismatch
        if (isset($whoisData['registrant_name']) && $user->name !== $whoisData['registrant_name']) {
            $flags[] = 'name_mismatch';
        }

        return $flags;
    }

    /**
     * Generate DNS instructions for the user
     */
    protected function generateDnsInstructions(string $token, string $domain): array
    {
        return [
            'dns_record_type' => 'TXT',
            'dns_record_name' => $domain,
            'dns_record_value' => $token,
            'ttl_recommendation' => 300,
            'instructions' => [
                '1. Log into your domain registrar or DNS provider',
                '2. Navigate to DNS management',
                '3. Add a new TXT record with the following details:',
                '4. Name/Host: ' . $domain,
                '5. Type: TXT',
                '6. Value: ' . $token,
                '7. TTL: 300 seconds (5 minutes)',
                '8. Save the record and wait for propagation',
            ],
        ];
    }

    /**
     * Rate limit verification attempts
     */
    public function isRateLimited(Domain $domain, User $user): bool
    {
        $key = "verification_attempts:domain:{$domain->id}:user:{$user->id}";
        $attempts = Cache::get($key, 0);
        
        $maxAttemptsPerHour = config('verification.rate_limit_per_hour', 6);
        
        if ($attempts >= $maxAttemptsPerHour) {
            return true;
        }

        Cache::put($key, $attempts + 1, 3600); // 1 hour
        return false;
    }
}