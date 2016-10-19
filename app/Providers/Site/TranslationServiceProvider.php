<?php

namespace App\Providers\Site;

use Models\Translation;
use App\Support\TranslationCollection;
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
            $trans = new Translation;

            $transCollection = new TranslationCollection;

            if ($trans->count() <= (int) $this->app['config']->get('cms.trans_limit')) {
                $transCollection->setCollection(
                    $trans->joinLanguages(true, false)->pluck('value', 'name')
                );
            }

            $this->app->instance('trans', $transCollection);

            view()->share('trans', $transCollection);
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
