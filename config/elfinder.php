<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public dir
    |--------------------------------------------------------------------------
    |
    | The public dir with extra options.
    |
    */

    'public' => [
        'files' => [
            'alias' => 'Public files',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem disks (Flysytem)
    |--------------------------------------------------------------------------
    |
    | Define an array of Filesystem disks, which use Flysystem.
    | You can set extra options, example:
    |
    | 'my-disk' => [
    |        'URL' => url('to/disk'),
    |        'alias' => 'Local storage',
    |    ]
    */

    'disks' => [],

    /*
    |--------------------------------------------------------------------------
    | Global roots options
    |--------------------------------------------------------------------------
    |
    | Roots global extra options for all of the Filesystem disks.
    |
    */

    'roots_options' => [
        'uploadAllow' => [
            // image
            'image/png', 'image/jpeg', 'image/gif', 'image/x-icon',
            // application
            'application/zip', 'application/x-rar', 'application/x-gzip', 'application/x-bzip2', 'application/x-tar',
            'application/x-7z-compressed', 'application/pdf', 'application/xml',
            'application/excel', 'application/mspowerpoint', 'application/msword',
            // docx
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // text
            'text/plain', 'text/html',
            // video
            'video/mp4', 'video/mpeg', 'video/x-msvideo',
            // audio
            'audio/mpeg'
        ],
        'uploadDeny'  => ['all'],
        'uploadOrder' => 'deny, allow'
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */

    'route' => [
        'prefix' => 'filemanager',
        'middleware' => 'CmsAuth', //Set to null to disable middleware filter
    ],

    /*
    |--------------------------------------------------------------------------
    | Access filter
    |--------------------------------------------------------------------------
    |
    | Filter callback to check the files
    |
    */

    'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

    /*
    |--------------------------------------------------------------------------
    | Roots
    |--------------------------------------------------------------------------
    |
    | By default, the roots file is LocalFileSystem, with the above public dir.
    | If you want custom options, you can set your own roots below.
    |
    */

    'roots' => [],

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | These options are merged, together with 'roots' and passed to the Connector.
    | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
    |
    */

    'options' => [],

];
