<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\DomainVerification;
use App\Models\User;
use App\Services\AuditService;
use App\Contracts\DnsResolverInterface;
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
     * Get verification instructions for a domain
     */
    public function getVerificationInstructions(Domain $domain): array
    {
        // Check if domain already has a verification record
        $verification = DomainVerification::where('domain_id', $domain->id)
            ->where('status', 'pending')
            ->first();

        if ($verification) {
            $dnsInstructions = $this->generateDnsInstructions($verification->token, $domain->full_domain);
            $hasWebsite = $this->checkWebsiteStatus($domain->full_domain);
            
            $instructions = [
                'method' => $verification->method,
                'status' => $verification->status,
                'token' => $verification->token,
                'verification_code' => $verification->token,
                'expires_at' => $verification->token_expires_at,
                'created_at' => $verification->created_at,
                'website_status' => [
                    'has_website' => $hasWebsite,
                    'url' => 'https://' . $domain->full_domain,
                ],
                'txt_record' => [
                    'type' => $dnsInstructions['dns_record_type'],
                    'name' => $dnsInstructions['dns_record_name'],
                    'value' => $dnsInstructions['dns_record_value'],
                    'instructions' => implode("\n", $dnsInstructions['instructions']),
                ],
                'cname_record' => [
                    'type' => 'CNAME',
                    'name' => 'verify.' . $domain->full_domain,
                    'value' => 'verification.flippdeal.com',
                    'instructions' => 'Alternative verification method using CNAME record. Point verify.yourdomain.com to verification.flippdeal.com',
                ],
            ];

            // Add file verification instructions if website is active
            if ($hasWebsite) {
                $instructions['file_verification'] = [
                    'filename' => 'verification.txt',
                    'url' => 'https://' . $domain->full_domain . '/verification.txt',
                    'content' => $verification->token,
                    'instructions' => 'Upload a file named verification.txt to your website root with the verification code.',
                ];
            }

            return $instructions;
        }

        // Return default instructions for creating new verification
        return [
            'method' => 'dns_txt',
            'status' => 'not_started',
            'verification_code' => 'Click "Generate Verification" to get your code',
            'expires_at' => null,
            'website_status' => [
                'has_website' => true, // Enable website verification by default
                'url' => 'https://' . $domain->full_domain,
            ],
            'file_verification' => [
                'filename' => 'verification.txt',
                'url' => 'https://' . $domain->full_domain . '/verification.txt',
                'content' => 'Click "Generate Verification" to get your verification code',
                'instructions' => 'Upload a file named verification.txt to your website root with the verification code.',
            ],
            'txt_record' => [
                'type' => 'TXT',
                'name' => $domain->full_domain,
                'value' => 'Click "Generate Verification" to get your token',
                'instructions' => 'Click the "Generate Verification" button below to create a verification token and get detailed instructions.',
            ],
            'cname_record' => [
                'type' => 'CNAME',
                'name' => 'verify.' . $domain->full_domain,
                'value' => 'verification.flippdeal.com',
                'instructions' => 'Alternative verification method using CNAME record. Point verify.yourdomain.com to verification.flippdeal.com',
            ],
        ];
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
     * Verify domain using file method
     */
    public function verifyDomainByFile(Domain $domain): bool
    {
        try {
            $instructions = $this->getVerificationInstructions($domain);
            
            // Check if file verification is available
            if (!isset($instructions['file_verification'])) {
                Log::info('File verification not available for domain: ' . $domain->full_domain);
                return false;
            }
            
            $fileUrl = $instructions['file_verification']['url'];
            $expectedContent = $instructions['file_verification']['content'];
            
            // Make HTTP request to check if file exists and contains correct content
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'GET',
                    'user_agent' => 'FlippDeal Domain Verifier/1.0',
                ]
            ]);
            
            $response = @file_get_contents($fileUrl, false, $context);
            
            if ($response === false) {
                Log::info('File verification failed - file not accessible: ' . $fileUrl);
                return false;
            }
            
            // Check if the file contains the verification code
            $isValid = trim($response) === trim($expectedContent);
            
            if ($isValid) {
                // Mark domain as verified
                $domain->update(['domain_verified' => true]);
                
                // Mark verification record as verified
                $verification = $this->getVerificationRecord($domain);
                if ($verification) {
                    $verification->update([
                        'status' => 'verified',
                        'evidence' => [
                            'verified_at' => now()->toISOString(),
                            'method' => 'file_upload',
                            'file_url' => $fileUrl,
                            'file_content' => $response
                        ]
                    ]);
                }
                
                Log::info('Domain verified successfully via file upload: ' . $domain->full_domain);
            } else {
                Log::info('File verification failed - content mismatch for: ' . $fileUrl);
            }
            
            return $isValid;
            
        } catch (\Exception $e) {
            Log::error('File verification failed for domain ' . $domain->full_domain . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate verification record for domain
     */
    public function generateVerificationRecord(Domain $domain): DomainVerification
    {
        // Check if domain already has a pending verification
        $existingVerification = DomainVerification::where('domain_id', $domain->id)
            ->where('status', 'pending')
            ->first();

        if ($existingVerification) {
            return $existingVerification;
        }

        // Generate unique token
        $token = Str::random(32);
        $expiresAt = now()->addMinutes($this->tokenTtl);

        // Check if domain has an active website
        $hasWebsite = $this->checkWebsiteStatus($domain->full_domain);

        // Create verification record with appropriate method
        $verification = DomainVerification::create([
            'domain_id' => $domain->id,
            'method' => $hasWebsite ? 'file_upload' : 'dns_txt',
            'token' => $token,
            'token_expires_at' => $expiresAt,
            'status' => 'pending',
            'attempts' => 0,
        ]);

        return $verification;
    }

    /**
     * Check if domain has an active website
     */
    private function checkWebsiteStatus(string $domain): bool
    {
        try {
            $url = 'https://' . $domain;
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'method' => 'HEAD',
                    'user_agent' => 'FlippDeal Domain Verifier/1.0',
                ]
            ]);
            
            $headers = @get_headers($url, 1, $context);
            return $headers && strpos($headers[0], '200') !== false;
        } catch (\Exception $e) {
            Log::info('Website status check failed for domain: ' . $domain, ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Regenerate verification record
     */
    public function regenerateVerification(Domain $domain): DomainVerification
    {
        // Delete existing verification records
        DomainVerification::where('domain_id', $domain->id)
            ->where('status', 'pending')
            ->delete();

        // Generate new verification record
        return $this->generateVerificationRecord($domain);
    }

    /**
     * Verify domain ownership
     */
    public function verifyDomainOwnership(Domain $domain): bool
    {
        $verification = $this->getVerificationRecord($domain);
        
        if (!$verification) {
            Log::info('No verification record found for domain: ' . $domain->full_domain);
            return false;
        }

        // Check verification based on method
        if ($verification->method === 'file_upload') {
            return $this->verifyDomainByFile($domain);
        } else {
            // Check DNS verification
            $dnsResult = $this->checkDnsVerification($verification);
            
            if ($dnsResult['success'] && $dnsResult['verified']) {
                // Mark domain as verified
                $domain->update(['domain_verified' => true]);
                
                // Mark verification record as verified
                $verification->update([
                    'status' => 'verified',
                    'evidence' => [
                        'verified_at' => now()->toISOString(),
                        'method' => 'dns_txt',
                        'dns_records_found' => $dnsResult['dns_records_found'] ?? []
                    ]
                ]);
                
                Log::info('Domain verified successfully via DNS: ' . $domain->full_domain);
                return true;
            }
        }

        Log::info('Domain verification failed for: ' . $domain->full_domain);
        return false;
    }

    /**
     * Get verification record for domain
     */
    public function getVerificationRecord(Domain $domain): ?DomainVerification
    {
        return DomainVerification::where('domain_id', $domain->id)
            ->where('status', 'pending')
            ->first();
    }

    /**
     * Check if verification is expired
     */
    public function isVerificationExpired(Domain $domain): bool
    {
        $verification = $this->getVerificationRecord($domain);
        
        if (!$verification) {
            return true;
        }
        
        return $verification->token_expires_at < now();
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