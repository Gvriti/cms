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

    'version' => '1.1.3',

    /*
    |--------------------------------------------------------------------------
    | CMS Slug
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
    | This array used to specify types of the page.
    |
    */

    'pages' => [
        'types' => [
            'text'        => 'Text',
            'collections' => 'Collections',
            'feedback'    => 'Feedback',
            'search'      => 'Search',
        ],
        'templates' => [],
        'attached' => [
            'collections'
        ],
        'implicit' => [
            'collections',
            'galleries'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Routes
    |--------------------------------------------------------------------------
    |
    | Here you can specify routes, which will also obtain additional routes.
    | See routes.php file for additional routes.
    |
    */

    'collection_routes' => [
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
    | This array used to specify collection types and its settings.
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
    | Inner Collections
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
    | Modules
    |--------------------------------------------------------------------------
    |
    | This array used to specify module types.
    |
    */

    'modules' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Methods
    |--------------------------------------------------------------------------
    |
    | This array used to specify types with methods, that will allow to
    | send a specific requests.
    |
    | For example: "post", "put", "delete".
    |
    */

    'methods' => [
        'post' => [
            'feedback@index' => 'send'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Tabs
    |--------------------------------------------------------------------------
    |
    | This array used to specify types, that will allow additional tab URIs.
    |
    | type => [
    |     'uri' => 'method'
    | ]
    |
    */

    'tabs' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | File Routes
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
    | CMS User Roles
    |--------------------------------------------------------------------------
    |
    | This array used to specify CMS user roles.
    |
    */

    'user_roles' => [
        'admin'  => 'Administrator',
        'member' => 'Member'
    ],

    /*
    |--------------------------------------------------------------------------
    | Translation Types
    |--------------------------------------------------------------------------
    |
    | The list of types that will filter translations.
    |
    */

    'trans_types' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Route Type Icons
    |--------------------------------------------------------------------------
    |
    | Set icons for all CMS route types.
    |
    */

    'icons' => [
        'menus'        => 'fa fa-list',
        'pages'        => 'fa fa-indent',
        'translations' => 'fa fa-language',

        'collections' => 'fa fa-list-alt',
        'catalog'     => 'fa fa-briefcase',
        'articles'    => 'fa fa-newspaper-o',
        'galleries'   => 'fa fa-th',
        'photos'      => 'fa fa-photo',
        'videos'      => 'fa fa-video-camera',

        'files'       => 'el el-paper-clip',

        'permissions' => 'fa fa-lock',
        'cms_users'   => 'fa fa-user-secret',
    ],

];
