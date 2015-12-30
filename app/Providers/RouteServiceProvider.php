<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The requested language.
     *
     * @var string
     */
    protected $language;

    /**
     * Indicates if the CMS routes should be loaded.
     *
     * @var bool
     */
    protected $cmsWillLoad = true;

    /**
     * Total count of the routable segments.
     *
     * @var int
     */
    protected $segmentsCount = 0;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        $config = $this->app['config'];

        if ($config->get('language_isset')) {
            $this->language = $config->get('app.language');
        }

        $this->cmsWillLoad = $config->get('cms_will_load');

        $this->segmentsCount = $config->get('url_segments_count');

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @param  bool  $cached
     * @return void
     */
    public function map(Router $router, $cached = false)
    {
        $router->group(['namespace' => $this->namespace], function ($router) use ($cached) {
            if (! $cached && ($this->app->runningInConsole() || $this->cmsWillLoad)) {
                require app_path('Http/routes.php');
            }

            require app_path('Http/siteRoutes.php');
        });

        $this->setDynamicRoutes($router);

        $this->filterRoutes($router);
    }

    /**
    * {@inheritdoc}
    */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function ($app) {
            if ($app->runningInConsole() || $this->cmsWillLoad) {
                require $this->app->getCachedRoutesPath();
            }

            $this->map($app['router'], true);
        });
    }

    /**
     * Set dynamic routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function setDynamicRoutes(Router $router)
    {
        $segments = '/';

        if ($this->segmentsCount && ! $this->cmsWillLoad) {
            for ($i = 1; $i <= $this->segmentsCount; $i++) { 
                $segments .= '{slug'.$i.'}/';
            }
        }

        $router->get($segments, [
            'as' => 'current', 'uses' => $this->namespace . '\Site\SiteController@run'
        ]);
    }

    /**
     * Add the specified language to all route URIs as a prefix.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function filterRoutes(Router $router)
    {
        if (! is_null($this->language)) {
            $routes = $router->getRoutes();

            foreach ($routes as $key => $route) {
                $route->prefix($this->language);
            }
        }
    }
}
