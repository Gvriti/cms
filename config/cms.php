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

    'version' => '2.0.3',

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
            'text' => 'Text',
            'collections' => 'Collections',
            'feedback' => 'Feedback',
            'search' => 'Search',
        ],
        'templates' => [],
        'attached' => [
            'collections'
        ],
        'implicit' => [
            'collections' => Models\Collection::class,
            'galleries' => Models\Gallery::class
        ],
        'explicit' => []
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Resource Routes
    |--------------------------------------------------------------------------
    |
    | Here you can specify routes, which will also obtain additional routes.
    |
    */

    'routes' => [
        'collections' => [
            'catalog' => 'AdminCatalogController',
            'articles' => 'AdminArticlesController',
            'galleries' => 'AdminGalleriesController',
            'faq' => 'AdminFaqController'
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
            'catalog' => 'Catalog',
            'articles' => 'Articles',
            'galleries' => 'Galleries',
            'faq' => 'FAQ'
        ],
        'order_by' => [
            'position' => 'Position',
            'created_at' => 'Creation date'
        ],
        'sort' => [
            'desc' => 'Descending',
            'asc' => 'Ascending'
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
                'position' => 'Position',
                'created_at' => 'Creation date'
            ],
            'sort' => [
                'desc' => 'Descending',
                'asc' => 'Ascending'
            ]
        ]
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

    'tabs' => [],

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
            'route_name' => 'pages',
            'foreign_key' => 'menu_id'
        ],
        'catalog' => [
            'route_name' => 'catalog',
            'foreign_key' => 'collection_id'
        ],
        'articles' => [
            'route_name' => 'articles',
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
        'admin' => 'Administrator',
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

    'trans_types' => [],

    /*
    |--------------------------------------------------------------------------
    | Translation Query Limit
    |--------------------------------------------------------------------------
    |
    | The limit of the translation query results. Query will be executed
    | one by one, if translation rows will be more than the specified limit.
    |
    */

    'trans_limit' => 80,

    /*
    |--------------------------------------------------------------------------
    | CMS Route Type Icons
    |--------------------------------------------------------------------------
    |
    | Set icons for all CMS route types.
    |
    */

    'icons' => [
        'dashboard' => 'fa fa-dashboard',

        'menus' => 'fa fa-list',
        'pages' => 'fa fa-indent',

        'collections' => 'fa fa-list-alt',
        'catalog' => 'fa fa-briefcase',
        'articles' => 'fa fa-newspaper-o',
        'faq' => 'fa fa-question-circle',

        'galleries' => 'fa fa-th',
        'photos' => 'fa fa-photo',
        'videos' => 'fa fa-video-camera',

        'taxis' => 'fa fa-taxi',
        'categories' => 'fa fa-list-alt',
        'objects' => 'fa fa-map-o',
        'branches' => 'fa fa-map-marker',
        'workingHours' => 'fa fa-clock-o',

        'permissions' => 'fa fa-lock',
        'cmsUsers' => 'fa fa-user-secret',
        'users' => 'fa fa-user',

        'translations' => 'fa fa-language',
        'files' => 'fa fa-paperclip',
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Bug Mail
    |--------------------------------------------------------------------------
    |
    | Here you can specify an e-mail address, where the user can send
    | bug reports.
    |
    */

    'bug_mail' => env('BUG_MAIL', null)

];
