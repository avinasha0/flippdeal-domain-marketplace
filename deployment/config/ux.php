<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enhanced UX Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for enhanced user experience features
    |
    */

    'enabled' => env('ENABLE_ENHANCED_UX', true),

    'verification_stepper' => [
        'enabled' => env('ENABLE_VERIFICATION_STEPPER', true),
        'show_actions' => env('VERIFICATION_STEPPER_SHOW_ACTIONS', true),
        'auto_refresh_seconds' => env('VERIFICATION_AUTO_REFRESH', 30),
    ],

    'escrow_checklist' => [
        'enabled' => env('ENABLE_ESCROW_CHECKLIST', true),
        'show_evidence_links' => env('ESCROW_CHECKLIST_SHOW_EVIDENCE', true),
        'require_evidence' => env('ESCROW_CHECKLIST_REQUIRE_EVIDENCE', true),
    ],

    'trust_signals' => [
        'enabled' => env('ENABLE_TRUST_SIGNALS', true),
        'show_verified_badges' => env('SHOW_VERIFIED_BADGES', true),
        'show_seller_ratings' => env('SHOW_SELLER_RATINGS', true),
        'show_trust_cards' => env('SHOW_TRUST_CARDS', true),
    ],

    'auction_countdown' => [
        'enabled' => env('ENABLE_AUCTION_COUNTDOWN', true),
        'show_timezone_info' => env('AUCTION_COUNTDOWN_SHOW_TIMEZONE', true),
        'auto_refresh_seconds' => env('AUCTION_COUNTDOWN_REFRESH', 1),
        'ending_soon_minutes' => env('AUCTION_ENDING_SOON_MINUTES', 5),
    ],

    'graceful_failures' => [
        'enabled' => env('ENABLE_GRACEFUL_FAILURES', true),
        'show_retry_buttons' => env('SHOW_RETRY_BUTTONS', true),
        'show_help_links' => env('SHOW_HELP_LINKS', true),
        'show_support_contact' => env('SHOW_SUPPORT_CONTACT', true),
    ],

    'help_pages' => [
        'enabled' => env('ENABLE_HELP_PAGES', true),
        'show_registrar_instructions' => env('HELP_SHOW_REGISTRAR_INSTRUCTIONS', true),
        'show_troubleshooting' => env('HELP_SHOW_TROUBLESHOOTING', true),
    ],

    'real_time_updates' => [
        'enabled' => env('ENABLE_REAL_TIME_UPDATES', true),
        'use_websockets' => env('USE_WEBSOCKETS', false),
        'fallback_polling_seconds' => env('FALLBACK_POLLING_SECONDS', 30),
    ],

    'accessibility' => [
        'enabled' => env('ENABLE_ACCESSIBILITY_FEATURES', true),
        'keyboard_navigation' => env('ENABLE_KEYBOARD_NAVIGATION', true),
        'screen_reader_support' => env('ENABLE_SCREEN_READER_SUPPORT', true),
        'high_contrast_mode' => env('ENABLE_HIGH_CONTRAST_MODE', false),
    ],

    'performance' => [
        'lazy_load_evidence' => env('LAZY_LOAD_EVIDENCE', true),
        'cache_help_pages' => env('CACHE_HELP_PAGES', true),
        'cache_ttl_minutes' => env('CACHE_TTL_MINUTES', 60),
    ],

];
