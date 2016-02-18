<?php

/*
|--------------------------------------------------------------------------
| Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the site.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$router->group(['namespace' => 'Site'], function ($router) {
    // glide server
    $router->get($this->app['config']->get('site.glide_base_url') . '/{path}', [
        'as' => 'glide', 'uses' => 'SiteGlideServerController@show'
    ])->where('path', '.+');
});

// translation requests from site
$router->group(['namespace' => 'Admin', 'middleware' => 'CmsAuth'], function ($router) {
    $router->get('!translations', ['as' => cms_prefix('translations.form'), 'uses' => 'AdminTranslationsController@getModal']);
    $router->post('!translations', ['as' => cms_prefix('translations.form'), 'uses' => 'AdminTranslationsController@postModal']);
});
