<?php

namespace App\Jobs;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WhoisLookupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [60, 120, 300]; // Retry after 1, 2, 5 minutes

    protected $domainId;
    protected $lookupType;
    protected $rateLimitKey = 'whois:rate_limit';

    /**
     * Create a new job instance.
     */
    public function __construct(int $domainId, string $lookupType = 'verification')
    {
        $this->domainId = $domainId;
        $this->lookupType = $lookupType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check rate limit
            if (!$this->checkRateLimit()) {
                Log::info('WHOIS lookup rate limited, delaying job', [
                    'domain_id' => $this->domainId,
                    'lookup_type' => $this->lookupType,
                ]);
                
                // Release job back to queue with delay
                $this->release(300); // 5 minutes
                return;
            }

            $domain = Domain::find($this->domainId);
            
            if (!$domain) {
                Log::warning('Domain not found for WHOIS lookup', [
                    'domain_id' => $this->domainId,
                ]);
                return;
            }

            Log::info('Starting WHOIS lookup', [
                'domain_id' => $this->domainId,
                'domain_name' => $domain->full_domain,
                'lookup_type' => $this->lookupType,
            ]);

            $whoisData = $this->performWhoisLookup($domain->full_domain);
            
            if ($whoisData) {
                $this->processWhoisData($domain, $whoisData);
                
                Log::info('WHOIS lookup completed successfully', [
                    'domain_id' => $this->domainId,
                    'domain_name' => $domain->full_domain,
                ]);
            } else {
                Log::warning('WHOIS lookup failed', [
                    'domain_id' => $this->domainId,
                    'domain_name' => $domain->full_domain,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WHOIS lookup job failed', [
                'domain_id' => $this->domainId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Check rate limit for WHOIS lookups
     */
    protected function checkRateLimit(): bool
    {
        try {
            $currentTime = time();
            $windowStart = $currentTime - 3600; // 1 hour window
            
            // Get current count for this hour
            $currentCount = Redis::zcount($this->rateLimitKey, $windowStart, $currentTime);
            
            $maxRequests = config('app.whois_rate_limit', 100); // 100 requests per hour
            
            if ($currentCount >= $maxRequests) {
                return false;
            }
            
            // Add current request to rate limit
            Redis::zadd($this->rateLimitKey, $currentTime, $currentTime . ':' . $this->domainId);
            Redis::expire($this->rateLimitKey, 3600); // Expire after 1 hour
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Rate limit check failed', [
                'error' => $e->getMessage(),
            ]);
            return true; // Allow request if rate limiting fails
        }
    }

    /**
     * Perform the actual WHOIS lookup
     */
    protected function performWhoisLookup(string $domainName): ?array
    {
        try {
            // Use a WHOIS service (this is a simplified example)
            // In production, you might want to use a paid WHOIS API service
            $whoisCommand = "whois {$domainName}";
            $output = shell_exec($whoisCommand);
            
            if (!$output) {
                return null;
            }
            
            return $this->parseWhoisOutput($output);
            
        } catch (\Exception $e) {
            Log::error('WHOIS lookup failed', [
                'domain_name' => $domainName,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Parse WHOIS output into structured data
     */
    protected function parseWhoisOutput(string $output): array
    {
        $data = [
            'raw_output' => $output,
            'parsed_at' => now()->toISOString(),
        ];
        
        // Parse common WHOIS fields
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || strpos($line, ':') === false) {
                continue;
            }
            
            [$key, $value] = explode(':', $line, 2);
            $key = strtolower(trim($key));
            $value = trim($value);
            
            switch ($key) {
                case 'registrar':
                case 'registrar name':
                    $data['registrar'] = $value;
                    break;
                    
                case 'registrant name':
                case 'organization':
                case 'org':
                    $data['owner'] = $value;
                    break;
                    
                case 'registrant email':
                case 'admin email':
                case 'email':
                    $data['email'] = $value;
                    break;
                    
                case 'creation date':
                case 'created':
                    $data['created_date'] = $value;
                    break;
                    
                case 'expiry date':
                case 'expires':
                    $data['expiry_date'] = $value;
                    break;
                    
                case 'updated date':
                case 'updated':
                    $data['updated_date'] = $value;
                    break;
                    
                case 'name servers':
                case 'nserver':
                    if (!isset($data['name_servers'])) {
                        $data['name_servers'] = [];
                    }
                    $data['name_servers'][] = $value;
                    break;
            }
        }
        
        return $data;
    }

    /**
     * Process the WHOIS data based on lookup type
     */
    protected function processWhoisData(Domain $domain, array $whoisData): void
    {
        switch ($this->lookupType) {
            case 'verification':
                $this->processVerificationLookup($domain, $whoisData);
                break;
                
            case 'analytics':
                $this->processAnalyticsLookup($domain, $whoisData);
                break;
                
            case 'transfer_check':
                $this->processTransferCheck($domain, $whoisData);
                break;
                
            default:
                Log::warning('Unknown WHOIS lookup type', [
                    'lookup_type' => $this->lookupType,
                    'domain_id' => $this->domainId,
                ]);
        }
    }

    /**
     * Process WHOIS data for verification
     */
    protected function processVerificationLookup(Domain $domain, array $whoisData): void
    {
        // Update domain with WHOIS data
        $domain->update([
            'whois_data' => $whoisData,
            'last_whois_check' => now(),
        ]);
        
        // Trigger verification checks if needed
        // This would integrate with your existing verification system
        Log::info('WHOIS data processed for verification', [
            'domain_id' => $domain->id,
            'registrar' => $whoisData['registrar'] ?? 'Unknown',
            'owner' => $whoisData['owner'] ?? 'Unknown',
        ]);
    }

    /**
     * Process WHOIS data for analytics
     */
    protected function processAnalyticsLookup(Domain $domain, array $whoisData): void
    {
        // Store WHOIS data for analytics
        $domain->update([
            'whois_data' => $whoisData,
            'last_whois_check' => now(),
        ]);
        
        // Could trigger additional analytics processing here
        Log::info('WHOIS data processed for analytics', [
            'domain_id' => $domain->id,
        ]);
    }

    /**
     * Process WHOIS data for transfer check
     */
    protected function processTransferCheck(Domain $domain, array $whoisData): void
    {
        // Check if domain has been transferred
        $currentRegistrar = $whoisData['registrar'] ?? '';
        $previousRegistrar = $domain->whois_data['registrar'] ?? '';
        
        if ($currentRegistrar && $previousRegistrar && $currentRegistrar !== $previousRegistrar) {
            Log::info('Domain transfer detected', [
                'domain_id' => $domain->id,
                'previous_registrar' => $previousRegistrar,
                'current_registrar' => $currentRegistrar,
            ]);
            
            // Trigger transfer verification process
            // This would integrate with your transfer verification system
        }
        
        $domain->update([
            'whois_data' => $whoisData,
            'last_whois_check' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('WhoisLookupJob failed permanently', [
            'domain_id' => $this->domainId,
            'lookup_type' => $this->lookupType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}