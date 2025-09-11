<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ReCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google ReCAPTCHA integration
    |
    */

    'enabled' => env('RECAPTCHA_ENABLED', false),
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'version' => env('RECAPTCHA_VERSION', 'v3'), // v2 or v3
    'threshold' => env('RECAPTCHA_THRESHOLD', 0.5), // For v3, score threshold

    'actions' => [
        'signup' => [
            'enabled' => env('RECAPTCHA_SIGNUP_ENABLED', true),
            'threshold' => env('RECAPTCHA_SIGNUP_THRESHOLD', 0.5),
        ],
        'login' => [
            'enabled' => env('RECAPTCHA_LOGIN_ENABLED', false),
            'threshold' => env('RECAPTCHA_LOGIN_THRESHOLD', 0.3),
        ],
        'verification' => [
            'enabled' => env('RECAPTCHA_VERIFICATION_ENABLED', true),
            'threshold' => env('RECAPTCHA_VERIFICATION_THRESHOLD', 0.5),
        ],
        'password_reset' => [
            'enabled' => env('RECAPTCHA_PASSWORD_RESET_ENABLED', true),
            'threshold' => env('RECAPTCHA_PASSWORD_RESET_THRESHOLD', 0.5),
        ],
        'contact' => [
            'enabled' => env('RECAPTCHA_CONTACT_ENABLED', true),
            'threshold' => env('RECAPTCHA_CONTACT_THRESHOLD', 0.5),
        ],
    ],

    'v2' => [
        'theme' => env('RECAPTCHA_V2_THEME', 'light'), // light or dark
        'size' => env('RECAPTCHA_V2_SIZE', 'normal'), // compact or normal
        'type' => env('RECAPTCHA_V2_TYPE', 'image'), // audio or image
    ],

    'v3' => [
        'action_timeout' => env('RECAPTCHA_V3_ACTION_TIMEOUT', 120), // seconds
        'score_threshold' => env('RECAPTCHA_V3_SCORE_THRESHOLD', 0.5),
    ],

    'testing' => [
        'enabled' => env('RECAPTCHA_TESTING', false),
        'test_site_key' => env('RECAPTCHA_TEST_SITE_KEY'),
        'test_secret_key' => env('RECAPTCHA_TEST_SECRET_KEY'),
    ],

];
