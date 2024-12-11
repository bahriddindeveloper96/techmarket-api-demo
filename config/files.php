<?php

return [
    'max_size' => env('MAX_FILE_SIZE', 2048), // KB
    'allowed_extensions' => ['jpeg', 'png', 'jpg', 'webp'],
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/webp'
    ],
    'storage_disk' => env('FILE_STORAGE_DISK', 'public'),
    'upload_rate_limit' => env('UPLOAD_RATE_LIMIT', '10,1'), // 10 requests per minute
    'scan_virus' => env('SCAN_FILES_FOR_VIRUS', true),
    'optimize_images' => env('OPTIMIZE_IMAGES', true),
    'max_filename_length' => 100,
    'path_blacklist' => ['..', '~', '.htaccess', 'web.config'],
];
