<?php

return [
    /**
     * Defines the root directory under the public disk where all media assets
     * are stored. Provide a relative path ending with a trailing slash.
     * Example: 'public/' or 'public/media/'.
     */
    'base_path' => 'public/',

    /**
     * Configuration for photo uploads. Use the 'path' option to set the
     * directory (relative to the base path) where photo files will be stored.
     * You may override the default via the APP_PHOTOS_PATH environment
     * variable.
     */
    'photos' => [
        'path' => env('APP_PHOTOS_PATH', 'photos/'),
    ],

    /**
     * Configuration for generic file uploads. Set the 'path' option to control
     * where files are stored relative to the base path. Override the default
     * using the APP_FILES_PATH environment variable.
     */
    'files' => [
        'path' => env('APP_FILES_PATH', 'files/'),
    ],

    /**
     * Configuration for video uploads. Adjust the 'path' option to change the
     * storage directory relative to the base path. Override the default using
     * the APP_VIDEOS_PATH environment variable.
     */
    'videos' => [
        'path' => env('APP_VIDEOS_PATH', 'videos/'),
    ],

    /**
     * Controls whether the package should automatically register its default
     * routes. Disable when you prefer to include the routes manually within
     * your application's routing files.
     */
    'load_routes' => true,

    /**
     * Route prefix applied to all media-serving endpoints exposed by the
     * package. Set to null or an empty string to register routes at the root
     * level.
     */
    'prefix' => 'api',

    /**
     * Global middleware stack applied to media-serving routes. Provide class
     * names or middleware aliases as defined in your application. Leave empty
     * to use no additional middleware.
     */
    'middleware' => [
        //
    ],

    /**
     * Per-directory middleware definitions for protected media paths when
     * serving files from storage. Use the syntax:
     * 'specific_directory_path_included_in_media_file_path' => [
     *     'middleware_1',
     *     'middleware_2',
     * ]
     * Each key must match a directory segment in the requested media path.
     */
    'protected_internal_media_base_paths' => [
        // '' => [],
    ],
];
