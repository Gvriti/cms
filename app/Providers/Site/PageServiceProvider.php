<?php

namespace App\Providers\Site;

use Models\Menu;
use Models\Page;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Do not boot if running in console to avoid artisan fail, when db table doesn't exists.
        // Do not boot if CMS is booted.
        if (! $this->app->runningInConsole() && ! cms_is_booted()) {
            $menu = (new menu)->where('main', 1)->first(['id']);

            $pages = [];

            if (! is_null($menu)) {
                $pages = (new page)->forSite($menu->id)->positionAsc()->get();
            }

            $this->app->instance('pagesTree', make_tree($pages, ''));
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
