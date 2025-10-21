<?php

namespace App\Services;

use App\Contracts\VirusScannerInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ClamAvVirusScanner implements VirusScannerInterface
{
    protected $clamdSocket;
    protected $timeout;

    public function __construct()
    {
        $this->clamdSocket = config('virus_scanner.clamd_socket', '/var/run/clamav/clamd.ctl');
        $this->timeout = config('virus_scanner.timeout', 30);
    }

    /**
     * Scan a file for viruses
     */
    public function scanFile(string $filePath): array
    {
        try {
            if (!$this->isAvailable()) {
                return [
                    'clean' => false,
                    'infected' => false,
                    'error' => 'ClamAV daemon not available',
                    'scan_time' => 0,
                ];
            }

            $startTime = microtime(true);
            
            $result = Process::timeout($this->timeout)
                ->run("clamdscan --no-summary --fdpass {$filePath}");

            $scanTime = microtime(true) - $startTime;

            if ($result->successful()) {
                // Check if file is clean
                $output = trim($result->output());
                $isClean = strpos($output, 'OK') !== false;
                $isInfected = strpos($output, 'FOUND') !== false;

                return [
                    'clean' => $isClean,
                    'infected' => $isInfected,
                    'threats' => $isInfected ? $this->extractThreats($output) : [],
                    'scan_time' => $scanTime,
                    'raw_output' => $output,
                ];
            } else {
                return [
                    'clean' => false,
                    'infected' => false,
                    'error' => 'Scan failed: ' . $result->errorOutput(),
                    'scan_time' => $scanTime,
                ];
            }

        } catch (\Exception $e) {
            Log::error('Virus scan exception', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            return [
                'clean' => false,
                'infected' => false,
                'error' => 'Scan exception: ' . $e->getMessage(),
                'scan_time' => 0,
            ];
        }
    }

    /**
     * Check if scanner is available
     */
    public function isAvailable(): bool
    {
        try {
            $result = Process::timeout(5)
                ->run("clamdscan --version");

            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get scanner version
     */
    public function getVersion(): ?string
    {
        try {
            $result = Process::timeout(5)
                ->run("clamdscan --version");

            if ($result->successful()) {
                return trim($result->output());
            }
        } catch (\Exception $e) {
            // Ignore
        }

        return null;
    }

    /**
     * Extract threat names from scan output
     */
    protected function extractThreats(string $output): array
    {
        $threats = [];
        $lines = explode("\n", $output);
        
        foreach ($lines as $line) {
            if (strpos($line, 'FOUND') !== false) {
                // Extract threat name from line like "file.txt: Trojan.Generic FOUND"
                $parts = explode(':', $line);
                if (count($parts) >= 2) {
                    $threatPart = trim($parts[1]);
                    $threatName = str_replace(' FOUND', '', $threatPart);
                    if (!empty($threatName)) {
                        $threats[] = $threatName;
                    }
                }
            }
        }
        
        return $threats;
    }
}
