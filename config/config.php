<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Storage Disk
    |--------------------------------------------------------------------------
    |
    | This option defines the default storage disk that will be used for
    | file uploads. You can use any disk configured in your filesystems.php
    | configuration file.
    |
    */
    'disk' => env('FILEMANAGER_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Upload Directory
    |--------------------------------------------------------------------------
    |
    | The directory where uploaded files will be stored. This path is
    | relative to the storage disk root.
    |
    */
    'upload_path' => env('FILEMANAGER_UPLOAD_PATH', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Directory
    |--------------------------------------------------------------------------
    |
    | The directory where thumbnails will be stored. This path is
    | relative to the storage disk root.
    |
    */
    'thumbnail_path' => env('FILEMANAGER_THUMBNAIL_PATH', 'uploads/thumbnails'),

    /*
    |--------------------------------------------------------------------------
    | Image Processing Settings
    |--------------------------------------------------------------------------
    |
    | Configure default image processing options including dimensions,
    | quality, and optimization settings.
    |
    */
    'image' => [
        'max_width' => env('FILEMANAGER_MAX_WIDTH', 1920),
        'max_height' => env('FILEMANAGER_MAX_HEIGHT', 1080),
        'quality' => env('FILEMANAGER_QUALITY', 85),
        'auto_optimize' => env('FILEMANAGER_AUTO_OPTIMIZE', true),
        'create_thumbnails' => env('FILEMANAGER_CREATE_THUMBNAILS', true),
        'thumbnail_width' => env('FILEMANAGER_THUMBNAIL_WIDTH', 300),
        'thumbnail_height' => env('FILEMANAGER_THUMBNAIL_HEIGHT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define validation rules for uploaded files including size limits,
    | allowed mime types, and file extensions.
    |
    */
    'validation' => [
        'max_file_size' => env('FILEMANAGER_MAX_FILE_SIZE', 10240), // KB
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'csv', 'zip', 'rar'
        ],
        'allowed_mime_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain', 'text/csv',
            'application/zip', 'application/x-rar-compressed'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generation
    |--------------------------------------------------------------------------
    |
    | Configure how URLs are generated for uploaded files.
    |
    */
    'url' => [
        'prefix' => env('FILEMANAGER_URL_PREFIX', '/storage'),
        'generate_absolute' => env('FILEMANAGER_ABSOLUTE_URLS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration options.
    |
    */
    'security' => [
        'scan_uploads' => env('FILEMANAGER_SCAN_UPLOADS', false),
        'quarantine_suspicious' => env('FILEMANAGER_QUARANTINE', false),
        'hash_filenames' => env('FILEMANAGER_HASH_FILENAMES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | The database table used to store media information.
    |
    */
    'table_name' => env('FILEMANAGER_TABLE_NAME', 'media'),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the API routes for the file manager.
    |
    */
    'routes' => [
        'enabled' => env('FILEMANAGER_ROUTES_ENABLED', true),
        'prefix' => env('FILEMANAGER_ROUTE_PREFIX', 'api/filemanager'),
        'middleware' => ['api'],
        'name_prefix' => 'filemanager.',
    ],
];