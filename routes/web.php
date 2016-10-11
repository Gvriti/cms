<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

$router->group(['namespace' => 'Site'], function ($router) {
    // glide server
    $router->get($this->app['config']->get('site.glide_base_url') . '/{path}', [
        'as' => 'glide', 'uses' => 'SiteGlideServerController@show'
    ])->where('path', '.+');
});

// translation form request
$router->group(['middleware' => 'cms.auth', 'namespace' => 'Admin', 'prefix' => cms_slug()], function ($router) {
    $router->get('translations/form', ['as' => 'translations.popup', 'uses' => 'AdminTranslationsController@getModal']);
    $router->post('translations/form', ['as' => 'translations.popup', 'uses' => 'AdminTranslationsController@postModal']);
});
