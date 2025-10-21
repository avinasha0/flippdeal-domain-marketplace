<?php

namespace App\Services;

use App\Contracts\VirusScannerInterface;

class StubVirusScanner implements VirusScannerInterface
{
    /**
     * Scan a file for viruses (stub implementation)
     */
    public function scanFile(string $filePath): array
    {
        // Simulate scan delay
        usleep(100000); // 0.1 seconds
        
        // For testing, randomly mark some files as infected
        $random = mt_rand(1, 100);
        
        if ($random <= 5) { // 5% chance of false positive for testing
            return [
                'clean' => false,
                'infected' => true,
                'threats' => ['Test.Threat.12345'],
                'scan_time' => 0.1,
                'raw_output' => "{$filePath}: Test.Threat.12345 FOUND",
            ];
        }
        
        return [
            'clean' => true,
            'infected' => false,
            'threats' => [],
            'scan_time' => 0.1,
            'raw_output' => "{$filePath}: OK",
        ];
    }

    /**
     * Check if scanner is available
     */
    public function isAvailable(): bool
    {
        return true; // Always available in stub mode
    }

    /**
     * Get scanner version
     */
    public function getVersion(): ?string
    {
        return 'StubVirusScanner v1.0.0 (Development)';
    }
}
