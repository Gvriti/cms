<?php

namespace App\Providers\Models;

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
        // Do not boot if we are running in the console to avoid migration fail.
        // Do not boot if CMS will load.
        if (! $this->app->runningInConsole() && ! cms_will_load()) {
            $menu = (new menu)->where('main', 1)->first(['id']);

            $pages = (new page)->forSite($menu ? $menu->id : $menu)->positionAsc()->get();

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
