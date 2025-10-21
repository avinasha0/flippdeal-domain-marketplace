<?php

namespace App\Contracts;

interface DnsResolverInterface
{
    /**
     * Get TXT records for a domain
     */
    public function getTxtRecords(string $domain): array;

    /**
     * Get CNAME records for a domain
     */
    public function getCnameRecords(string $domain): array;

    /**
     * Get WHOIS data for a domain
     */
    public function getWhoisData(string $domain): ?array;

    /**
     * Check if domain has specific TXT record
     */
    public function hasTxtRecord(string $domain, string $value): bool;

    /**
     * Check if domain has specific CNAME record
     */
    public function hasCnameRecord(string $domain, string $value): bool;
}
