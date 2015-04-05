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
            if (cms_will_load()) {
                // CMS settings
                $settings = $this->app['db']->table('cms_settings')->first();

                if (! is_null($settings)) {
                    $settings->body = "$settings->sidebar_direction $settings->layout_boxed $settings->skin_sidebar  $settings->skin_user_menu $settings->skin_horizontal";
                    $settings->body = preg_replace('/\s+/', ' ', trim($settings->body));
                }

                $settings = new Collection($settings);

                view()->share(['settings' => $settings]);
            } else {
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
