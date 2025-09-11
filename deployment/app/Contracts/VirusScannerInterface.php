<?php

namespace App\Contracts;

interface VirusScannerInterface
{
    /**
     * Scan a file for viruses
     */
    public function scanFile(string $filePath): array;

    /**
     * Check if scanner is available
     */
    public function isAvailable(): bool;

    /**
     * Get scanner version
     */
    public function getVersion(): ?string;
}
