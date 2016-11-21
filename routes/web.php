<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['namespace' => 'Site'], function ($router) {
//    $router->group(['middleware' => 'site'], function ($router) {
//
//    });

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
