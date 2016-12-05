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

$router->group(['namespace' => 'Web'], function ($router) {
    // glide server
    $router->get($this->app['config']->get('web.glide_base_url', '!img') . '/{path}', [
        'as' => 'glide', 'uses' => 'WebGlideServerController@show'
    ])->where('path', '.+');
});

// translation form request
$router->group(['middleware' => 'cms.auth', 'namespace' => 'Admin', 'prefix' => cms_slug()], function ($router) {
    $router->get('translations/form', ['as' => 'translations.popup', 'uses' => 'AdminTranslationsController@getModal']);
    $router->post('translations/form', ['as' => 'translations.popup', 'uses' => 'AdminTranslationsController@postModal']);
});
