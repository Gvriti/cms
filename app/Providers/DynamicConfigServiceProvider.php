<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DynamicConfigServiceProvider extends ServiceProvider
{
    /**
     * Set all necessary dynamic configuration.
     *
     * @return void
     */
    public function boot()
    {
        $config = $this->app['config'];

        $request = $this->app['request'];

        $segmentsCount = count($segments = $request->segments());

        $language = $request->segment(1);

        $languagesCount = count($languages = $config->get('app.languages'));

        if ($languagesCount > 1 && array_key_exists($language, $languages)) {
            $config->set(['app.language' => $language]);
            $config->set(['language_isset' => true]);

            $segmentsCount--;

            array_shift($segments);
        } else {
            $config->set(['language_isset' => false]);
        }

        $config->set(['route_segments_count' => $segmentsCount]);

        $cmsWillLoad = current($segments) == $config->get('cms.slug');

        $config->set(['cms_will_load' => $cmsWillLoad]);

        if (! $cmsWillLoad) {
            $config->set(['app.locale' => $config->get('app.language')]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
