<?php

namespace App\Providers\Admin;

use Models\Menu;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Do not boot if running in console to avoid artisan fail, when db table doesn't exists.
        // Boot if CMS is booted.
        if (! $this->app->runningInConsole() && cms_is_booted()) {
            $menus = (new Menu)->get();

            view()->composer([
                'admin.partials.sidebar_menu',
                'admin.partials.horizontal_menu',
                'admin.menus.index',
                'admin.pages.index'
            ], function($view) use ($menus) {
                $view->with('menus', $menus);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
