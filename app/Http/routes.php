<?php

/*
|--------------------------------------------------------------------------
| CMS Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the CMS.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// authentication
$router->group(['prefix' => cms_slug(), 'namespace' => 'Admin\Auth'], function($router) {
    // login
    $router->get('login', ['as' => cms_prefix('login'), 'uses' => 'AdminAuthController@getLogin']);
    $router->post('login', ['as' => cms_prefix('login'), 'uses' => 'AdminAuthController@postLogin']);
    $router->get('logout', ['as' => cms_prefix('logout'), 'uses' => 'AdminAuthController@getLogout']);

    // lockscreen
    $router->get('lockscreen', ['as' => cms_prefix('lockscreen'), 'uses' => 'AdminAuthController@getLockscreen']);
    $router->post('lockscreen', ['as' => cms_prefix('lockscreen'), 'uses' => 'AdminAuthController@postLockscreen']);
    $router->put('lockscreen', ['as' => cms_prefix('lockscreen'), 'uses' => 'AdminAuthController@setLockscreen']);
});

// CMS
$router->group(['prefix' => cms_slug(), 'namespace' => 'Admin', 'middleware' => 'CmsAuth'], function($router) {
    // dashboard
    $router->get('/', ['as' => cms_prefix('dashboard'), 'uses' => 'AdminDashboardController@index']);

    // menus
    $router->post('menus/set-main', ['as' => cms_prefix('menus.setMain'), 'uses' => 'AdminMenusController@setMain']);
    $router->resource('menus', 'AdminMenusController', ['names' => cms_prefix('menus', true),
        'except' => ['show']
    ]);

    // pages
    $router->post('pages/position', ['as' => cms_prefix('pages.updatePosition'), 'uses' => 'AdminPagesController@updatePosition']);
    $router->post('pages/visibility/{id}', ['as' => cms_prefix('pages.visibility'), 'uses' => 'AdminPagesController@visibility']);
    $router->put('pages/move/{menuId}', ['as' => cms_prefix('pages.move'), 'uses' => 'AdminPagesController@move']);
    $router->put('pages/collapse', ['as' => cms_prefix('pages.collapse'), 'uses' => 'AdminPagesController@collapse']);
    $router->resource('menus.pages', 'AdminPagesController', ['names' => cms_prefix('pages', true),
        'except' => ['show']
    ]);

    // collections
    $router->resource('collections', 'AdminCollectionsController', ['names' => cms_prefix('collections', true),
        'except' => ['show']
    ]);
    // collection types
    foreach ($this->app['config']->get('cms.collection.types') as $key => $value)  {
        $router->post($key . '/position', [
            'as' => cms_prefix($key . '.updatePosition'),
            'uses' => $value['controller'] . '@updatePosition'
        ]);
        $router->post($key . '/visibility/{id}', [
            'as' => cms_prefix($key . '.visibility'),
            'uses' => $value['controller'] . '@visibility'
        ]);
        $router->put($key . '/move/{id}', [
            'as' => cms_prefix($key . '.move'),
            'uses' => $value['controller'] . '@move'
        ]);
        $router->resource('collections.' . $key, $value['controller'], ['names' => cms_prefix($key, true),
            'except' => ['show']
        ]);
        // nested collection types
        if (isset($value['nested'])) {
            foreach ($value['nested'] as $nestKey => $nestValue)  {
                $router->post($nestKey . '/position', [
                    'as' => cms_prefix($nestKey . '.updatePosition'),
                    'uses' => $nestValue['controller'] . '@updatePosition'
                ]);
                $router->post($nestKey . '/visibility/{id}', [
                    'as' => cms_prefix($nestKey . '.visibility'),
                    'uses' => $nestValue['controller'] . '@visibility'
                ]);
                $router->resource($key . '.' . $nestKey, $nestValue['controller'], ['names' => cms_prefix($nestKey, true),
                    'except' => ['show']
                ]);
            }
        }
    }

    // attached files
    $router->post('files/position', ['as' => cms_prefix('files.updatePosition'), 'uses' => 'AdminFilesController@updatePosition']);
    $router->post('files/visibility/{id}', ['as' => cms_prefix('files.visibility'), 'uses' => 'AdminFilesController@visibility']);
    $router->resource('{routeName}/{routeId}/files', 'AdminFilesController', ['names' => cms_prefix('files', true),
        'except' => ['show']
    ]);

    // slider
    $router->post('slider/position', ['as' => cms_prefix('slider.updatePosition'), 'uses' => 'AdminSliderController@updatePosition']);
    $router->post('slider/visibility/{id}', ['as' => cms_prefix('slider.visibility'), 'uses' => 'AdminSliderController@visibility']);
    $router->resource('slider', 'AdminSliderController', ['names' => cms_prefix('slider', true),
        'except' => ['show']
    ]);

    // localization
    $router->resource('localization', 'AdminLocalizationController', ['names' => cms_prefix('localization', true),
        'except' => ['show']
    ]);

    // notes
    $router->get('notes', ['as' => cms_prefix('notes.index'), 'uses' => 'AdminNotesController@index']);
    $router->put('notes', ['as' => cms_prefix('notes.save'), 'uses' => 'AdminNotesController@save']);
    $router->post('notes', ['as' => cms_prefix('notes.destroy'), 'uses' => 'AdminNotesController@destroy']);
    $router->post('notes-calendar', ['as' => cms_prefix('notes.calendar'), 'uses' => 'AdminNotesController@calendar']);

    // calendar
    $router->get('calendar', ['as' => cms_prefix('calendar.index'), 'uses' => 'AdminCalendarController@index']);
    $router->post('calendar/events', ['as' => cms_prefix('calendar.events'), 'uses' => 'AdminCalendarController@events']);
    $router->put('calendar', ['as' => cms_prefix('calendar.save'), 'uses' => 'AdminCalendarController@save']);
    $router->post('calendar', ['as' => cms_prefix('calendar.destroy'), 'uses' => 'AdminCalendarController@destroy']);

    // admin Settings
    $router->get('settings', ['as' => cms_prefix('settings.index'), 'uses' => 'AdminSettingsController@index']);
    $router->put('settings', ['as' => cms_prefix('settings.update'), 'uses' => 'AdminSettingsController@update']);
    $router->get('site-settings', ['as' => cms_prefix('siteSettings.index'), 'uses' => 'AdminSiteSettingsController@index']);
    $router->put('site-settings', ['as' => cms_prefix('siteSettings.update'), 'uses' => 'AdminSiteSettingsController@update']);

    // file manager
    $router->get('filemanager', ['as' => cms_prefix('filemanager'), 'uses' => 'AdminFilemanagerController@index']);

    // cms users
    $router->resource('cms-users', 'AdminCmsUsersController', ['names' => cms_prefix('cmsUsers', true)]);
    // cms user permissions
    $router->get('cms-users/{id}/permissions', ['as' => cms_prefix('permissions.index'), 'uses' => 'AdminPermissionsController@index']);
    $router->post('cms-users/{id}/permissions', ['as' => cms_prefix('permissions.store'), 'uses' => 'AdminPermissionsController@store']);

    // bug report
    $router->get('bug-report', ['as' => cms_prefix('bugReport.index'), 'uses' => 'AdminBugReportController@index']);
    $router->post('bug-report', ['as' => cms_prefix('bugReport.send'), 'uses' => 'AdminBugReportController@send']);
});
