<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Verification Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for domain ownership verification
    |
    */

    'token_ttl_minutes' => env('DOMAIN_VERIFICATION_TOKEN_TTL_MINUTES', 120),
    'max_attempts' => env('DOMAIN_VERIFICATION_MAX_ATTEMPTS', 12),
    'rate_limit_per_hour' => env('VERIFICATION_RATE_LIMIT_PER_HOUR', 6),
    'use_redis_cache' => env('USE_REDIS_VERIFICATION_CACHE', true),

    'methods' => [
        'dns_txt' => [
            'enabled' => true,
            'priority' => 1,
            'description' => 'DNS TXT Record Verification',
        ],
        'dns_cname' => [
            'enabled' => true,
            'priority' => 2,
            'description' => 'DNS CNAME Record Verification',
        ],
        'file_upload' => [
            'enabled' => true,
            'priority' => 3,
            'description' => 'File Upload Verification',
        ],
        'whois' => [
            'enabled' => true,
            'priority' => 4,
            'description' => 'WHOIS Data Verification',
        ],
    ],

    'dns' => [
        'timeout' => env('DNS_TIMEOUT', 10),
        'retries' => env('DNS_RETRIES', 3),
        'ttl_recommendation' => 300, // 5 minutes
    ],

    'whois' => [
        'timeout' => env('WHOIS_TIMEOUT', 30),
        'retries' => env('WHOIS_RETRIES', 2),
    ],

    'admin_review' => [
        'enabled' => true,
        'notify_admins' => true,
        'auto_approve_after_hours' => 24, // Auto-approve after 24 hours if no admin action
    ],

];
