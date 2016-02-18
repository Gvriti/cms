<?php

namespace App\Providers\Models;

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
        // Do not boot if we are running in the console to avoid migration fail.
        // Do not boot if CMS will load.
        if (! $this->app->runningInConsole() && ! cms_will_load()) {
            $trans = (new Translation)->joinLanguages()
                                      ->get()
                                      ->lists('value', 'name');

            $this->app->instance('trans', $trans);

            view()->share('trans', $trans);
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
