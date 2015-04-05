<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LanguageComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $languages = $this->makeLanguageUrls();

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
     * Make current URL for all available languages of the aplication.
     *
     * @return array
     */
    private function makeLanguageUrls()
    {
        $languageList = [];

        $config = $this->app['config'];

        $languagesCount = count($languages = $config->get('app.languages'));

        $request = $this->app['request'];

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
