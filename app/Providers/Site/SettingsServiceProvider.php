<?php

namespace App\Providers\Site;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
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
            $settings = new Collection(
                $this->app['db']->table('site_settings')->first()
            );

            view()->share(['siteSettings' => $settings]);
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
