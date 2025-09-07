<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\Verification;
use App\Services\DomainVerificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeriodicVerificationCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;
    public $backoff = [300, 600, 900]; // Retry after 5, 10, 15 minutes

    protected $verificationService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->verificationService = app(DomainVerificationService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting periodic verification check');

            // Get pending verifications that are older than 1 hour
            $pendingVerifications = Verification::where('status', 'pending')
                ->where('created_at', '<', now()->subHour())
                ->with('domain')
                ->get();

            if ($pendingVerifications->isEmpty()) {
                Log::info('No pending verifications found');
                return;
            }

            $processedCount = 0;
            $successCount = 0;
            $failureCount = 0;

            foreach ($pendingVerifications as $verification) {
                try {
                    $processedCount++;
                    
                    Log::debug('Processing verification', [
                        'verification_id' => $verification->id,
                        'domain_id' => $verification->domain_id,
                        'type' => $verification->type,
                    ]);

                    $result = $this->processVerification($verification);
                    
                    if ($result['success']) {
                        $successCount++;
                        Log::info('Verification completed successfully', [
                            'verification_id' => $verification->id,
                            'result' => $result,
                        ]);
                    } else {
                        $failureCount++;
                        Log::warning('Verification failed', [
                            'verification_id' => $verification->id,
                            'error' => $result['error'] ?? 'Unknown error',
                        ]);
                    }

                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error('Error processing verification', [
                        'verification_id' => $verification->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Periodic verification check completed', [
                'processed' => $processedCount,
                'successful' => $successCount,
                'failed' => $failureCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Periodic verification check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Process a single verification
     */
    protected function processVerification(Verification $verification): array
    {
        $domain = $verification->domain;
        
        if (!$domain) {
            return [
                'success' => false,
                'error' => 'Domain not found',
            ];
        }

        try {
            switch ($verification->type) {
                case 'dns':
                    return $this->verifyDnsRecord($verification, $domain);
                
                case 'file':
                    return $this->verifyFileUpload($verification, $domain);
                
                case 'whois':
                    return $this->verifyWhoisData($verification, $domain);
                
                default:
                    return [
                        'success' => false,
                        'error' => 'Unknown verification type',
                    ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify DNS record
     */
    protected function verifyDnsRecord(Verification $verification, Domain $domain): array
    {
        $dnsRecord = $verification->metadata['dns_record'] ?? null;
        $expectedValue = $verification->metadata['expected_value'] ?? null;

        if (!$dnsRecord || !$expectedValue) {
            return [
                'success' => false,
                'error' => 'Missing DNS record or expected value',
            ];
        }

        $isValid = $this->verificationService->verifyDnsRecord($domain->full_domain, $dnsRecord, $expectedValue);
        
        $verification->update([
            'status' => $isValid ? 'verified' : 'failed',
            'verified_at' => $isValid ? now() : null,
            'metadata' => array_merge($verification->metadata, [
                'last_checked_at' => now()->toISOString(),
                'is_valid' => $isValid,
            ]),
        ]);

        return [
            'success' => true,
            'verified' => $isValid,
        ];
    }

    /**
     * Verify file upload
     */
    protected function verifyFileUpload(Verification $verification, Domain $domain): array
    {
        $filePath = $verification->metadata['file_path'] ?? null;
        $expectedContent = $verification->metadata['expected_content'] ?? null;

        if (!$filePath || !$expectedContent) {
            return [
                'success' => false,
                'error' => 'Missing file path or expected content',
            ];
        }

        $isValid = $this->verificationService->verifyFileUpload($filePath, $expectedContent);
        
        $verification->update([
            'status' => $isValid ? 'verified' : 'failed',
            'verified_at' => $isValid ? now() : null,
            'metadata' => array_merge($verification->metadata, [
                'last_checked_at' => now()->toISOString(),
                'is_valid' => $isValid,
            ]),
        ]);

        return [
            'success' => true,
            'verified' => $isValid,
        ];
    }

    /**
     * Verify WHOIS data
     */
    protected function verifyWhoisData(Verification $verification, Domain $domain): array
    {
        $expectedOwner = $verification->metadata['expected_owner'] ?? null;
        $expectedRegistrar = $verification->metadata['expected_registrar'] ?? null;

        if (!$expectedOwner && !$expectedRegistrar) {
            return [
                'success' => false,
                'error' => 'Missing expected owner or registrar',
            ];
        }

        $whoisData = $this->verificationService->getWhoisData($domain->full_domain);
        
        if (!$whoisData) {
            return [
                'success' => false,
                'error' => 'Failed to retrieve WHOIS data',
            ];
        }

        $isValid = true;
        if ($expectedOwner && !str_contains(strtolower($whoisData['owner'] ?? ''), strtolower($expectedOwner))) {
            $isValid = false;
        }
        if ($expectedRegistrar && !str_contains(strtolower($whoisData['registrar'] ?? ''), strtolower($expectedRegistrar))) {
            $isValid = false;
        }

        $verification->update([
            'status' => $isValid ? 'verified' : 'failed',
            'verified_at' => $isValid ? now() : null,
            'metadata' => array_merge($verification->metadata, [
                'last_checked_at' => now()->toISOString(),
                'is_valid' => $isValid,
                'whois_data' => $whoisData,
            ]),
        ]);

        return [
            'success' => true,
            'verified' => $isValid,
        ];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('PeriodicVerificationCheckJob failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}