<?php

namespace App\Providers;

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
        // Do not boot if we are running in the console to avoid migration fail.
        if (! $this->app->runningInConsole()) {
            if (! cms_will_load()) {
                // Site settings
                $settings = $this->app['db']->table('site_settings')->first();

                $settings = new Collection($settings);

                view()->share(['siteSettings' => $settings]);
            }
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
