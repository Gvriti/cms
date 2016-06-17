<?php

namespace App\Providers\Site;

use Models\Translation;
use Illuminate\Support\Collection;
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
        // Do not boot if CMS is booted.
        if (! cms_is_booted()) {
            if (! $this->app->runningInConsole()) {
                $trans = (new Translation)->joinLanguages()->get();
            } else {
                $trans = new Collection;
            }

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
