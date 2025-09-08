<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DNS Resolution Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for DNS resolution services used in domain verification.
    |
    */

    'timeout' => env('DNS_TIMEOUT', 10),
    'retries' => env('DNS_RETRIES', 3),
    'cache_ttl' => env('DNS_CACHE_TTL', 300), // 5 minutes
    
    /*
    |--------------------------------------------------------------------------
    | DNS Servers
    |--------------------------------------------------------------------------
    |
    | Custom DNS servers to use for resolution. If empty, system defaults are used.
    |
    */
    
    'servers' => [
        // '8.8.8.8',
        // '1.1.1.1',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting for DNS queries to prevent abuse.
    |
    */
    
    'rate_limit' => [
        'max_queries_per_minute' => env('DNS_RATE_LIMIT', 60),
        'max_queries_per_hour' => env('DNS_RATE_LIMIT_HOUR', 1000),
    ],
];
