<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Verification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DomainVerificationService
{
    /**
     * Generate a verification record for domain ownership.
     */
    public function generateVerificationRecord(Domain $domain): Verification
    {
        $verificationCode = $this->generateVerificationCode();
        
        return Verification::create([
            'user_id' => $domain->user_id,
            'type' => 'domain_ownership',
            'status' => 'pending',
            'verification_data' => [
                'domain_id' => $domain->id,
                'domain_name' => $domain->full_domain,
                'verification_code' => $verificationCode,
                'verification_method' => 'dns_txt',
                'txt_record' => "domain-verification={$verificationCode}",
                'cname_record' => "verify.{$domain->full_domain}",
                'cname_target' => "verification.domainmarketplace.com"
            ],
            'verification_code' => $verificationCode,
            'expires_at' => now()->addDays(7)
        ]);
    }

    /**
     * Verify domain ownership using DNS records.
     */
    public function verifyDomainOwnership(Domain $domain): bool
    {
        $verification = $this->getDomainVerification($domain);
        
        if (!$verification) {
            return false;
        }

        $verificationData = $verification->verification_data;
        
        // Try TXT record verification first
        if ($this->verifyTxtRecord($domain->full_domain, $verificationData['txt_record'])) {
            $this->markDomainAsVerified($domain, $verification, 'dns_txt');
            return true;
        }

        // Try CNAME record verification
        if ($this->verifyCnameRecord($verificationData['cname_record'], $verificationData['cname_target'])) {
            $this->markDomainAsVerified($domain, $verification, 'dns_cname');
            return true;
        }

        return false;
    }

    /**
     * Verify TXT record for domain.
     */
    private function verifyTxtRecord(string $domain, string $expectedRecord): bool
    {
        try {
            $txtRecords = dns_get_record($domain, DNS_TXT);
            
            foreach ($txtRecords as $record) {
                if (in_array($expectedRecord, $record['txt'])) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("DNS TXT verification failed for {$domain}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify CNAME record for domain.
     */
    private function verifyCnameRecord(string $cname, string $expectedTarget): bool
    {
        try {
            $cnameRecords = dns_get_record($cname, DNS_CNAME);
            
            foreach ($cnameRecords as $record) {
                if ($record['target'] === $expectedTarget) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("DNS CNAME verification failed for {$cname}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark domain as verified.
     */
    private function markDomainAsVerified(Domain $domain, Verification $verification, string $method): void
    {
        $domain->update([
            'domain_verified' => true,
            'verified_at' => now(),
            'verification_method' => $method
        ]);

        $verification->markAsVerified();
        
        Log::info("Domain {$domain->full_domain} verified using {$method}");
    }

    /**
     * Get domain verification record.
     */
    private function getDomainVerification(Domain $domain): ?Verification
    {
        return Verification::where('user_id', $domain->user_id)
            ->where('type', 'domain_ownership')
            ->where('status', 'pending')
            ->whereJsonContains('verification_data->domain_id', $domain->id)
            ->first();
    }

    /**
     * Generate a unique verification code.
     */
    private function generateVerificationCode(): string
    {
        return Str::random(32);
    }

    /**
     * Get verification instructions for a domain.
     */
    public function getVerificationInstructions(Domain $domain): array
    {
        $verification = $this->getDomainVerification($domain);
        
        if (!$verification) {
            return [];
        }

        $data = $verification->verification_data;

        return [
            'txt_record' => [
                'type' => 'TXT',
                'name' => $domain->full_domain,
                'value' => $data['txt_record'],
                'instructions' => "Add a TXT record to your domain's DNS settings with the above name and value."
            ],
            'cname_record' => [
                'type' => 'CNAME',
                'name' => $data['cname_record'],
                'value' => $data['cname_target'],
                'instructions' => "Add a CNAME record to your domain's DNS settings with the above name and value."
            ],
            'verification_code' => $data['verification_code'],
            'expires_at' => $verification->expires_at
        ];
    }

    /**
     * Check if domain verification is expired.
     */
    public function isVerificationExpired(Domain $domain): bool
    {
        $verification = $this->getDomainVerification($domain);
        
        return $verification && $verification->isExpired();
    }

    /**
     * Regenerate verification record for domain.
     */
    public function regenerateVerification(Domain $domain): Verification
    {
        // Mark existing verification as expired
        $existingVerification = $this->getDomainVerification($domain);
        if ($existingVerification) {
            $existingVerification->update(['status' => 'expired']);
        }

        // Generate new verification
        return $this->generateVerificationRecord($domain);
    }
}
