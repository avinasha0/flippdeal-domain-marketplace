<?php

namespace App\Services;

use App\Contracts\DnsResolverInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DnsResolverService implements DnsResolverInterface
{
    protected $timeout;
    protected $retries;

    public function __construct()
    {
        $this->timeout = config('dns.timeout', 10);
        $this->retries = config('dns.retries', 3);
    }

    /**
     * Get TXT records for a domain
     */
    public function getTxtRecords(string $domain): array
    {
        try {
            $result = $this->executeDigCommand($domain, 'TXT');
            
            if (!$result['success']) {
                Log::warning('DNS TXT lookup failed', [
                    'domain' => $domain,
                    'error' => $result['error'],
                ]);
                return [];
            }

            return $this->parseTxtRecords($result['output']);

        } catch (\Exception $e) {
            Log::error('DNS TXT lookup exception', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get CNAME records for a domain
     */
    public function getCnameRecords(string $domain): array
    {
        try {
            $result = $this->executeDigCommand($domain, 'CNAME');
            
            if (!$result['success']) {
                Log::warning('DNS CNAME lookup failed', [
                    'domain' => $domain,
                    'error' => $result['error'],
                ]);
                return [];
            }

            return $this->parseCnameRecords($result['output']);

        } catch (\Exception $e) {
            Log::error('DNS CNAME lookup exception', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get WHOIS data for a domain
     */
    public function getWhoisData(string $domain): ?array
    {
        try {
            $result = Process::timeout($this->timeout)
                ->run("whois {$domain}");

            if (!$result->successful()) {
                Log::warning('WHOIS lookup failed', [
                    'domain' => $domain,
                    'error' => $result->errorOutput(),
                ]);
                return null;
            }

            return $this->parseWhoisData($result->output());

        } catch (\Exception $e) {
            Log::error('WHOIS lookup exception', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if domain has specific TXT record
     */
    public function hasTxtRecord(string $domain, string $value): bool
    {
        $records = $this->getTxtRecords($domain);
        return in_array(trim($value), array_map('trim', $records));
    }

    /**
     * Check if domain has specific CNAME record
     */
    public function hasCnameRecord(string $domain, string $value): bool
    {
        $records = $this->getCnameRecords($domain);
        return in_array(trim($value), array_map('trim', $records));
    }

    /**
     * Execute dig command
     */
    protected function executeDigCommand(string $domain, string $type): array
    {
        $attempts = 0;
        
        while ($attempts < $this->retries) {
            try {
                $result = Process::timeout($this->timeout)
                    ->run("dig +short {$type} {$domain}");

                if ($result->successful()) {
                    return [
                        'success' => true,
                        'output' => $result->output(),
                    ];
                }

                $attempts++;
                
                if ($attempts < $this->retries) {
                    sleep(1); // Wait before retry
                }

            } catch (\Exception $e) {
                $attempts++;
                
                if ($attempts >= $this->retries) {
                    return [
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
                
                sleep(1);
            }
        }

        return [
            'success' => false,
            'error' => 'Max retries exceeded',
        ];
    }

    /**
     * Parse TXT records from dig output
     */
    protected function parseTxtRecords(string $output): array
    {
        $records = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Remove quotes from TXT record
            $record = trim($line, '"');
            if (!empty($record)) {
                $records[] = $record;
            }
        }
        
        return $records;
    }

    /**
     * Parse CNAME records from dig output
     */
    protected function parseCnameRecords(string $output): array
    {
        $records = [];
        $lines = explode("\n", trim($output));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Remove trailing dot from CNAME record
            $record = rtrim($line, '.');
            if (!empty($record)) {
                $records[] = $record;
            }
        }
        
        return $records;
    }

    /**
     * Parse WHOIS data
     */
    protected function parseWhoisData(string $output): array
    {
        $data = [];
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, ':') === false) continue;
            
            [$key, $value] = explode(':', $line, 2);
            $key = strtolower(trim($key));
            $value = trim($value);
            
            if (empty($value)) continue;
            
            switch ($key) {
                case 'registrar':
                case 'registrar name':
                    $data['registrar'] = $value;
                    break;
                    
                case 'registrant name':
                case 'organization':
                case 'org':
                    $data['registrant_name'] = $value;
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
}
