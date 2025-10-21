<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for analytics and metrics collection
    |
    */

    'view_counting' => [
        'enabled' => env('USE_REDIS_VIEW_COUNT', true),
        'redis_prefix' => 'domain:views',
        'aggregation_interval' => 'hourly', // hourly, daily
        'retention_days' => 30,
    ],

    'metrics' => [
        'daily_aggregation_time' => '00:00', // UTC time for daily aggregation
        'hourly_aggregation_time' => '00', // Minute of hour for hourly aggregation
        'backfill_enabled' => env('ANALYTICS_BACKFILL_ENABLED', false),
    ],

    'thresholds' => [
        'high_volume' => env('AML_HIGH_VOLUME_THRESHOLD', 50000), // $50,000
        'rapid_transfers' => env('AML_RAPID_TRANSFER_THRESHOLD', 5), // 5 transfers
        'multiple_high_value' => env('AML_MULTIPLE_HIGH_VALUE_THRESHOLD', 3), // 3 transactions
        'kyc_threshold' => env('KYC_THRESHOLD', 10000), // $10,000
    ],

    'whois' => [
        'rate_limit' => env('WHOIS_RATE_LIMIT', 100), // requests per hour
        'timeout' => env('WHOIS_TIMEOUT', 30), // seconds
        'retry_attempts' => env('WHOIS_RETRY_ATTEMPTS', 3),
    ],

    'verification' => [
        'check_interval' => env('VERIFICATION_CHECK_INTERVAL', 30), // minutes
        'max_attempts' => env('VERIFICATION_MAX_ATTEMPTS', 10),
        'timeout' => env('VERIFICATION_TIMEOUT', 300), // seconds
    ],

    'notifications' => [
        'email_batch_size' => env('EMAIL_BATCH_SIZE', 100),
        'email_delay' => env('EMAIL_DELAY', 1), // seconds between emails
        'priority_queues' => [
            'high' => 'emails-high',
            'normal' => 'emails',
            'low' => 'emails-low',
        ],
    ],

];
