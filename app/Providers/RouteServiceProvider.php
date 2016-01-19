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
            require app_path('Http/siteRoutes.php');

            if (! $cached && ($this->app->runningInConsole() || $this->cmsWillLoad)) {
                require app_path('Http/routes.php');
            }
        });

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
