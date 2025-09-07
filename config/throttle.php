<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Throttling Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for rate limiting and throttling
    |
    */

    'login' => [
        'max_attempts' => env('THROTTLE_LOGIN_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('THROTTLE_LOGIN_DECAY_MINUTES', 15),
        'key' => 'login',
    ],

    'verification' => [
        'max_attempts' => env('THROTTLE_VERIFICATION_MAX_ATTEMPTS', 6),
        'decay_minutes' => env('THROTTLE_VERIFICATION_DECAY_MINUTES', 1),
        'key' => 'verification',
    ],

    'search' => [
        'max_attempts' => env('THROTTLE_SEARCH_MAX_ATTEMPTS', 30),
        'decay_minutes' => env('THROTTLE_SEARCH_DECAY_MINUTES', 1),
        'key' => 'search',
    ],

    'bidding' => [
        'max_attempts' => env('THROTTLE_BIDDING_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('THROTTLE_BIDDING_DECAY_MINUTES', 1),
        'key' => 'bidding',
    ],

    'messaging' => [
        'max_attempts' => env('THROTTLE_MESSAGING_MAX_ATTEMPTS', 20),
        'decay_minutes' => env('THROTTLE_MESSAGING_DECAY_MINUTES', 1),
        'key' => 'messaging',
    ],

    'file_upload' => [
        'max_attempts' => env('THROTTLE_FILE_UPLOAD_MAX_ATTEMPTS', 10),
        'decay_minutes' => env('THROTTLE_FILE_UPLOAD_DECAY_MINUTES', 1),
        'key' => 'file_upload',
    ],

    'api' => [
        'max_attempts' => env('THROTTLE_API_MAX_ATTEMPTS', 60),
        'decay_minutes' => env('THROTTLE_API_DECAY_MINUTES', 1),
        'key' => 'api',
    ],

    'bot_detection' => [
        'enabled' => env('BOT_DETECTION_ENABLED', true),
        'suspicious_ips' => env('BOT_DETECTION_SUSPICIOUS_IPS', ''),
        'max_requests_per_minute' => env('BOT_DETECTION_MAX_REQUESTS_PER_MINUTE', 30),
        'max_requests_per_hour' => env('BOT_DETECTION_MAX_REQUESTS_PER_HOUR', 1000),
    ],

    'exponential_backoff' => [
        'enabled' => env('EXPONENTIAL_BACKOFF_ENABLED', true),
        'base_delay_seconds' => env('EXPONENTIAL_BACKOFF_BASE_DELAY', 1),
        'max_delay_seconds' => env('EXPONENTIAL_BACKOFF_MAX_DELAY', 300),
        'multiplier' => env('EXPONENTIAL_BACKOFF_MULTIPLIER', 2),
    ],

];
