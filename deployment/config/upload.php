<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for secure file uploads
    |
    */

    'max_size_mb' => env('MAX_UPLOAD_SIZE_MB', 10),
    'allowed_mimes' => explode(',', env('ALLOWED_UPLOAD_MIMES', 'image/jpeg,image/png,image/gif,application/pdf')),
    'allowed_extensions' => explode(',', env('ALLOWED_UPLOAD_EXTENSIONS', 'jpg,jpeg,png,gif,pdf')),

    'storage' => [
        'disk' => env('UPLOAD_STORAGE_DISK', 's3'),
        'path_prefix' => env('UPLOAD_PATH_PREFIX', 'uploads'),
        'private' => true,
    ],

    'image_processing' => [
        'strip_exif' => env('STRIP_IMAGE_EXIF', true),
        'max_width' => env('MAX_IMAGE_WIDTH', 2048),
        'max_height' => env('MAX_IMAGE_HEIGHT', 2048),
        'quality' => env('IMAGE_QUALITY', 85),
    ],

    'virus_scanning' => [
        'enabled' => env('VIRUS_SCAN_ENABLED', true),
        'provider' => env('VIRUS_SCAN_PROVIDER', 'clamav'), // clamav, external, none
        'timeout' => env('VIRUS_SCAN_TIMEOUT', 30),
        'quarantine_infected' => env('QUARANTINE_INFECTED_FILES', true),
    ],

    'clamav' => [
        'socket' => env('CLAMAV_SOCKET', '/var/run/clamav/clamd.ctl'),
        'timeout' => env('CLAMAV_TIMEOUT', 30),
    ],

    'signed_urls' => [
        'expiration_minutes' => env('SIGNED_URL_EXPIRATION_MINUTES', 60),
        'max_expiration_hours' => env('SIGNED_URL_MAX_EXPIRATION_HOURS', 24),
    ],

    'security' => [
        'reject_executable' => env('REJECT_EXECUTABLE_FILES', true),
        'reject_script_files' => env('REJECT_SCRIPT_FILES', true),
        'scan_uploaded_files' => env('SCAN_UPLOADED_FILES', true),
    ],

];
