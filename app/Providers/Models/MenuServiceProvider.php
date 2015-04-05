<?php

namespace App\Providers\Models;

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
        // Do not boot if we are running in the console to avoid migration fail.
        // Do not boot if CMS will not load.
        if (! $this->app->runningInConsole() && cms_will_load()) {
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
