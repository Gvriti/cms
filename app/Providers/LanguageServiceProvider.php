<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    public function boot(Request $request, Repository $config)
    {
        $this->setLanguageConfig($request, $config);

        $languages = $this->makeLanguageUrls($request, $config);

        view()->composer([
            'admin.partials.user',
            'admin.partials.horizontal_menu',
            'site.partials.head',
            'site.partials.header'
        ], function($view) use ($languages) {
            $view->with('languages', $languages);
        });
    }

    /**
     * Set language config.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return void
     */
    protected function setLanguageConfig(Request $request, Repository $config)
    {
        $segmentsCount = count($segments = $request->segments());

        $firstSegment = $request->segment(1);

        $languagesCount = count($languages = $config->get('app.languages'));

        // Set current application language dynamically
        if ($languagesCount > 1 && array_key_exists($firstSegment, $languages)) {
            $config->set(['app.language' => $firstSegment]);
            $config->set(['language_isset' => true]);

            $segmentsCount--;

            array_shift($segments);
        } else {
            $config->set(['language_isset' => false]);
        }

        // Set URL segments and its count, without language segment
        $config->set(['url_segments' => $segments]);
        $config->set(['url_segments_count' => $segmentsCount]);

        $cmsWillLoad = current($segments) == $config->get('cms.slug');

        $config->set(['cms_will_load' => $cmsWillLoad]);

        if (! $cmsWillLoad) {
            $config->set(['app.locale' => $config->get('app.language')]);
        }
    }

    /**
     * Make current URL for all available languages of the aplication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @return array
     */
    protected function makeLanguageUrls(Request $request, Repository $config)
    {
        $languageList = [];

        $languagesCount = count($languages = $config->get('app.languages'));

        $segments = $request->segments();

        if ($languagesCount > 1 && (empty($segments) || ! array_key_exists($segments[0], $languages))) {
            array_unshift($segments, $config->get('app.language'));
        }

        $query = $request->query();

        $query = $query ? '?' . http_build_query($query) : null;

        foreach($languages as $key => $value) {
            if ($languagesCount > 1) {
                $segments[0] = $key;
            }

            $languageList[$key]['url'] = $request->root() . '/' . implode('/', $segments) . $query;

            $languageList[$key]['name'] = $value;
        }

        return $languageList;
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
