<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$router->group(['middleware' => 'cms.settings', 'prefix' => cms_slug()], function ($router) {
    // authentication
    $router->group(['namespace' => 'Auth'], function ($router) {
        // login
        $router->get('login', ['as' => 'login', 'uses' => 'AuthAdminController@getLogin']);
        $router->post('login', ['as' => 'login', 'uses' => 'AuthAdminController@postLogin']);
        $router->get('logout', ['as' => 'logout', 'uses' => 'AuthAdminController@getLogout']);

        // lockscreen
        $router->group(['middleware' => ['cms.lockscreen']], function ($router) {
            $router->get('lockscreen', ['as' => 'lockscreen', 'uses' => 'AuthAdminController@getLockscreen']);
            $router->post('lockscreen', ['as' => 'lockscreen', 'uses' => 'AuthAdminController@postLockscreen']);
            $router->put('lockscreen', ['as' => 'lockscreen', 'uses' => 'AuthAdminController@setLockscreen']);
        });
    });

    // CMS
    $router->group(['middleware' => ['cms.auth'], 'namespace' => 'Admin'], function ($router) {
        // dashboard
        $router->get('/', ['as' => 'dashboard', 'uses' => 'AdminDashboardController@index']);

        // menus
        $router->post('menus/set-main', ['as' => 'menus.setMain', 'uses' => 'AdminMenusController@setMain']);
        $router->resource('menus', 'AdminMenusController', ['names' => resource_names('menus'),
            'except' => ['show']
        ]);

        // pages
        $router->post('pages/visibility/{id}', ['as' => 'pages.visibility', 'uses' => 'AdminPagesController@visibility']);
        $router->post('pages/position', ['as' => 'pages.updatePosition', 'uses' => 'AdminPagesController@updatePosition']);
        $router->post('pages/templates', ['as' => 'pages.templates', 'uses' => 'AdminPagesController@getTemplates']);
        $router->put('pages/move/{menuId}', ['as' => 'pages.move', 'uses' => 'AdminPagesController@move']);
        $router->put('pages/collapse', ['as' => 'pages.collapse', 'uses' => 'AdminPagesController@collapse']);
        $router->resource('menus.pages', 'AdminPagesController', ['names' => resource_names('pages'),
            'except' => ['show']
        ]);

        // collections
        $router->resource('collections', 'AdminCollectionsController', ['names' => resource_names('collections'),
            'except' => ['show']
        ]);
        // routes from config
        foreach ($this->app['config']->get('cms.collection_routes') as $prefix => $routes) {
            foreach ($routes as $route => $controller) {
                $router->post($route . '/visibility/{id}', [
                    'as' => $route . '.visibility',
                    'uses' => $controller . '@visibility'
                ]);
                $router->post($route . '/position', [
                    'as' => $route . '.updatePosition',
                    'uses' => $controller . '@updatePosition'
                ]);
                $router->put($route . '/move/{id}', [
                    'as' => $route . '.move',
                    'uses' => $controller . '@move'
                ]);
                $router->resource($prefix . '.' . $route, $controller, ['names' => resource_names($route),
                    'except' => ['show']
                ]);
            }
        }

        // attached files
        $router->post('files/visibility/{id}', ['as' => 'files.visibility', 'uses' => 'AdminFilesController@visibility']);
        $router->post('files/position', ['as' => 'files.updatePosition', 'uses' => 'AdminFilesController@updatePosition']);
        $router->resource('{routeName}/{routeId}/files', 'AdminFilesController', ['names' => resource_names('files'),
            'except' => ['show']
        ]);

        // slider
        $router->post('slider/visibility/{id}', ['as' => 'slider.visibility', 'uses' => 'AdminSliderController@visibility']);
        $router->post('slider/position', ['as' => 'slider.updatePosition', 'uses' => 'AdminSliderController@updatePosition']);
        $router->resource('slider', 'AdminSliderController', ['names' => resource_names('slider'),
            'except' => ['show']
        ]);

        // translations
        $router->resource('translations', 'AdminTranslationsController', ['names' => resource_names('translations'),
            'except' => ['show']
        ]);

        // notes
        $router->get('notes', ['as' => 'notes.index', 'uses' => 'AdminNotesController@index']);
        $router->put('notes', ['as' => 'notes.save', 'uses' => 'AdminNotesController@save']);
        $router->post('notes', ['as' => 'notes.destroy', 'uses' => 'AdminNotesController@destroy']);
        $router->post('notes-calendar', ['as' => 'notes.calendar', 'uses' => 'AdminNotesController@calendar']);

        // calendar
        $router->get('calendar', ['as' => 'calendar.index', 'uses' => 'AdminCalendarController@index']);
        $router->post('calendar/events', ['as' => 'calendar.events', 'uses' => 'AdminCalendarController@events']);
        $router->put('calendar', ['as' => 'calendar.save', 'uses' => 'AdminCalendarController@save']);
        $router->post('calendar', ['as' => 'calendar.destroy', 'uses' => 'AdminCalendarController@destroy']);

        // admin Settings
        $router->get('settings', ['as' => 'settings.index', 'uses' => 'AdminSettingsController@index']);
        $router->put('settings', ['as' => 'settings.update', 'uses' => 'AdminSettingsController@update']);
        $router->get('site-settings', ['as' => 'siteSettings.index', 'uses' => 'AdminSiteSettingsController@index']);
        $router->put('site-settings', ['as' => 'siteSettings.update', 'uses' => 'AdminSiteSettingsController@update']);

        // file manager
        $router->get('filemanager', ['as' => 'filemanager', 'uses' => 'AdminFilemanagerController@index']);

        // cms users
        $router->resource('cms-users', 'AdminCmsUsersController', ['names' => resource_names('cmsUsers')]);
        // cms user permissions
        $router->get('cms-users/{id}/permissions', ['as' => 'permissions.index', 'uses' => 'AdminPermissionsController@index']);
        $router->post('cms-users/{id}/permissions', ['as' => 'permissions.store', 'uses' => 'AdminPermissionsController@store']);

        // bug report
        $router->get('bug-report', ['as' => 'bugReport.index', 'uses' => 'AdminBugReportController@index']);
        $router->post('bug-report', ['as' => 'bugReport.send', 'uses' => 'AdminBugReportController@send']);
    });
});
