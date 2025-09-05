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
     * Verify domain ownership using DNS records or file verification.
     */
    public function verifyDomainOwnership(Domain $domain): bool
    {
        $verification = $this->getDomainVerification($domain);
        
        if (!$verification) {
            return false;
        }

        $verificationData = $verification->verification_data;
        
        // Try file verification first (if domain has active website)
        if ($this->verifyDomainByFile($domain)) {
            return true;
        }
        
        // Try TXT record verification
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
     * Generate verification file content for file-based verification.
     */
    public function generateVerificationFileContent(string $verificationCode): string
    {
        return "<!-- FlippDeal Domain Verification -->\n" .
               "<!-- Verification Code: {$verificationCode} -->\n" .
               "<!-- Generated on: " . now()->toISOString() . " -->\n" .
               "<!DOCTYPE html>\n" .
               "<html>\n" .
               "<head>\n" .
               "    <title>Domain Verification - FlippDeal</title>\n" .
               "    <meta name=\"robots\" content=\"noindex, nofollow\">\n" .
               "</head>\n" .
               "<body>\n" .
               "    <h1>Domain Verification</h1>\n" .
               "    <p>This file is used to verify domain ownership for FlippDeal.</p>\n" .
               "    <p>Verification Code: <strong>{$verificationCode}</strong></p>\n" .
               "    <p>Generated: " . now()->format('Y-m-d H:i:s T') . "</p>\n" .
               "</body>\n" .
               "</html>";
    }

    /**
     * Verify domain ownership using file-based verification.
     */
    public function verifyDomainByFile(Domain $domain): bool
    {
        $verification = $this->getDomainVerification($domain);
        
        if (!$verification) {
            return false;
        }

        $verificationData = $verification->verification_data;
        $verificationCode = $verificationData['verification_code'];
        
        // Try to fetch the verification file from the domain
        $verificationUrl = "https://{$domain->full_domain}/flippdeal-verification.html";
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'FlippDeal-Verification/1.0',
                    'follow_location' => true,
                    'max_redirects' => 3
                ]
            ]);
            
            $content = file_get_contents($verificationUrl, false, $context);
            
            if ($content === false) {
                return false;
            }
            
            // Check if the verification code is present in the file
            if (strpos($content, $verificationCode) !== false) {
                $this->markDomainAsVerified($domain, $verification, 'file_verification');
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("File verification failed for {$domain->full_domain}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if domain has an active website (HTTP response).
     */
    public function checkDomainWebsiteStatus(string $domain): array
    {
        $urls = [
            "https://{$domain}",
            "http://{$domain}",
            "https://www.{$domain}",
            "http://www.{$domain}"
        ];
        
        foreach ($urls as $url) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'FlippDeal-Verification/1.0',
                        'follow_location' => true,
                        'max_redirects' => 2
                    ]
                ]);
                
                $headers = get_headers($url, 1, $context);
                
                if ($headers && isset($headers[0])) {
                    $statusCode = (int) substr($headers[0], 9, 3);
                    
                    if ($statusCode >= 200 && $statusCode < 400) {
                        return [
                            'has_website' => true,
                            'url' => $url,
                            'status_code' => $statusCode,
                            'method' => 'file_verification'
                        ];
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return [
            'has_website' => false,
            'url' => null,
            'status_code' => null,
            'method' => 'dns_verification'
        ];
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
        $websiteStatus = $this->checkDomainWebsiteStatus($domain->full_domain);

        $instructions = [
            'verification_code' => $data['verification_code'],
            'expires_at' => $verification->expires_at,
            'website_status' => $websiteStatus
        ];

        // Add file verification if domain has active website
        if ($websiteStatus['has_website']) {
            $instructions['file_verification'] = [
                'method' => 'file_upload',
                'filename' => 'flippdeal-verification.html',
                'url' => $websiteStatus['url'] . '/flippdeal-verification.html',
                'content' => $this->generateVerificationFileContent($data['verification_code']),
                'instructions' => "Upload the verification file to your website's root directory (public_html, www, or htdocs folder)."
            ];
        }

        // Add DNS verification methods
        $instructions['txt_record'] = [
            'type' => 'TXT',
            'name' => $domain->full_domain,
            'value' => $data['txt_record'],
            'instructions' => "Add a TXT record to your domain's DNS settings with the above name and value."
        ];

        $instructions['cname_record'] = [
            'type' => 'CNAME',
            'name' => $data['cname_record'],
            'value' => $data['cname_target'],
            'instructions' => "Add a CNAME record to your domain's DNS settings with the above name and value."
        ];

        return $instructions;
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
