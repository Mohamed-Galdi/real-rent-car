<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The disk where files will be stored permanently
    |
    */
    'storage_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Temporary Files Path
    |--------------------------------------------------------------------------
    |
    | Path where temporary files are stored before being moved to permanent location
    |
    */
    'temp_path' => 'temp-files',

    /*
    |--------------------------------------------------------------------------
    | Files Path
    |--------------------------------------------------------------------------
    |
    | Base path where permanent files are stored
    |
    */
    'files_path' => 'files',

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Files larger than this size (in bytes) will be uploaded in chunks.
    |
    */
    'chunk_size' => 10 * 1024 * 1024, // 10MB


    /*
    |--------------------------------------------------------------------------
    | Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the FilePond locale. Set to null for English (default).
    | Supported locales: 'ar', 'fr', 'es', null (English)
    |
    */
    'locale' => null,

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the package routes
    |
    */
    'routes' => [
        'prefix' => 'filepond',
        'middleware' => ['web'],
    ],
];
