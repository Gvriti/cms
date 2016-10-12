<?php

namespace App\Providers\Site;

use Models\Translation;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
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
            $trans = (new Translation)->joinLanguages()->get();

            $this->app->instance('trans', $trans);

            view()->share('trans', $trans->pluck('value', 'name'));
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
