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
     * The CMS slug.
     *
     * @var string
     */
    protected $cmsSlug;

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

        $this->cmsSlug = $config->get('cms.slug');

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/siteRoutes.php');
            require app_path('Http/routes.php');
        });

        $this->filterRoutes($router);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCachedRoutes()
    {
        parent::loadCachedRoutes();

        $this->app->booted(function () {
            $this->filterRoutes($this->app['router']);
        });
    }

    /**
     * Filter all routes by specified language and cms slug.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function filterRoutes(Router $router)
    {
        $routes = $router->getRoutes();

        foreach ($routes as $key => $route) {
            if (! is_null($this->language)) {
                $route->prefix($this->language);
            }

            if (str_contains($route->getPrefix(), $this->cmsSlug)) {
                $route->name('.' . $this->cmsSlug);
            }
        }
    }
}
