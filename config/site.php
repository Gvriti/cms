<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site home text
    |--------------------------------------------------------------------------
    |
    | The array of home text, translated for all available languages.
    |
    */

    'home' => [
        'ka' => 'მთავარი',
        'ge' => 'მთავარი',
        'en' => 'Home',
        'ru' => 'Главная'
    ],

    /*
    |--------------------------------------------------------------------------
    | Glide settings
    |--------------------------------------------------------------------------
    |
    | This array used to define glide settings. Key should be a view name.
    | Base url is for site routes.
    |
    */

    'glide_base_url' => '!img',

    'glide' => [
        'articles'     => ['w' => 320, 'h' => 200, 'fit' => 'crop'],
        'article'      => ['w' => 570, 'h' => 290, 'fit' => 'crop'],
        'catalog'      => ['w' => 320, 'h' => 200, 'fit' => 'crop'],
        'catalog-item' => ['w' => 570, 'h' => 290, 'fit' => 'crop'],
        'gallery'      => ['w' => 320, 'h' => 200, 'fit' => 'crop'],
        'photos'       => ['w' => 270, 'h' => 180, 'fit' => 'crop'],
        'text'         => ['w' => 570, 'h' => 290, 'fit' => 'crop'],
        'files'        => ['w' => 270, 'h' => 180, 'fit' => 'crop'],
    ],

    'glide_crop' => [
        'center'       => 'Center',
        'top-left'     => 'Top left',
        'top'          => 'Top',
        'top-right'    => 'Top right',
        'left'         => 'Left',
        'right'        => 'Right',
        'bottom-left'  => 'Bottom left',
        'bottom'       => 'Bottom',
        'bottom-right' => 'Bottom right'
    ],

];
