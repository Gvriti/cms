<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Version
    |--------------------------------------------------------------------------
    |
    | Version of the content management system (CMS).
    |
    */

    'version' => '1.0.0-rc1',

    /*
    |--------------------------------------------------------------------------
    | CMS slug
    |--------------------------------------------------------------------------
    |
    | Here you should specify the cms slug for the application.
    |
    */

    'slug' => '!cms',

    /*
    |--------------------------------------------------------------------------
    | CMS User roles
    |--------------------------------------------------------------------------
    |
    | This array used to define CMS user roles.
    |
    */

    'user_roles' => [
        'admin'  => 'Administrator',
        'member' => 'Member'
    ],

    /*
    |--------------------------------------------------------------------------
    | Collection types
    |--------------------------------------------------------------------------
    |
    | This array used to define types, controllers, slugs and
    | route names for the collection types.
    |
    */

    'collection' => [
        'types' => [
            'catalog' => [
                'controller' => 'AdminCatalogController'
            ],
            'articles' => [
                'controller' => 'AdminArticlesController'
            ],
            'galleries' => [
                'controller' => 'AdminGalleriesController',
                'nested'     => [
                    'photos' => [
                        'controller' => 'AdminPhotosController'
                    ],
                    'videos' => [
                        'controller' => 'AdminVideosController'
                    ]
                ]
            ]
        ],
        'order_by' => [
            'position'   => 'Position',
            'created_at' => 'Creation date'
        ],
        'sort' => [
            'desc' => 'Descending',
            'asc'  => 'Ascending'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Inner collection types
    |--------------------------------------------------------------------------
    |
    | The array of the collection types that has a parent collection.
    |
    */

    'inner_collection' => ['galleries'],

    /*
    |--------------------------------------------------------------------------
    | Gallery settings
    |--------------------------------------------------------------------------
    |
    | This array used to define gallery types and sort order..
    |
    */

    'gallery' => [
        'types' => [
            'photos' => 'Photos',
            'videos' => 'Videos'
        ],
        'order_by' => [
            'position'   => 'Position',
            'created_at' => 'Creation date'
        ],
        'sort' => [
            'desc' => 'Descending',
            'asc'  => 'Ascending'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Page types
    |--------------------------------------------------------------------------
    |
    | This array used to define types of the page.
    |
    */

    'page' => [
        'types' => [
            'text'       => 'Text',
            'collection' => 'Collection',
            'feedback'   => 'Feedback',
            'search'     => 'Search',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | File route names
    |--------------------------------------------------------------------------
    |
    | The array of file route names, that has an access to the attached files.
    | Route names can also contain a foreign key.
    |
    */

    'files' => [
        'pages' => [
            'foreign_key' => 'menu_id'
        ],
        'catalog' => [
            'foreign_key' => 'collection_id'
        ],
        'articles' => [
            'foreign_key' => 'collection_id'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS route type icons
    |--------------------------------------------------------------------------
    |
    | Set icons for all CMS route types.
    |
    */

    'icons' => [
        'menus'       => 'fa fa-list',
        'pages'       => 'fa fa-indent',
        'collections' => 'fa fa-list-alt',
        'galleries'   => 'fa fa-th-large',
        'catalog'     => 'fa fa-briefcase',
        'articles'    => 'fa fa-newspaper-o',
        'photos'      => 'fa fa-photo',
        'videos'      => 'fa fa-video-camera',
        'options'     => 'fa fa-list-ol',
        'files'       => 'el el-paper-clip'
    ],

];
