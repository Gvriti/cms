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
    | Pages
    |--------------------------------------------------------------------------
    |
    | This array used to define types of the page.
    |
    */

    'pages' => [
        'types' => [
            'text'       => 'Text',
            'collection' => 'Collection',
            'feedback'   => 'Feedback',
            'search'     => 'Search',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Routes
    |--------------------------------------------------------------------------
    |
    | Here you can define a specific routes, which will also obtain additional.
    |
    */

    'routes' => [
        'collections' => [
            'catalog'   => 'AdminCatalogController',
            'articles'  => 'AdminArticlesController',
            'galleries' => 'AdminGalleriesController'
        ],
        'galleries' => [
            'photos' => 'AdminPhotosController',
            'videos' => 'AdminVideosController'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Collections
    |--------------------------------------------------------------------------
    |
    | This array used to define collection settings.
    |
    */

    'collections' => [
        'types' => [
            'catalog'   => 'Catalog',
            'articles'  => 'Articles',
            'galleries' => 'Galleries'
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
    | Inner collections
    |--------------------------------------------------------------------------
    |
    | The array of the collection types that has a parent collection.
    |
    */

    'inner_collections' => [
        'galleries' => [
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
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | File routes
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
