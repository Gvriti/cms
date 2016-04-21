<?php

namespace App\Providers\Site;

use Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class DynamicRouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the dynamic routes.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers\Site';

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The prefix of the routes URI.
     *
     * @var string
     */
    protected $uriPrefix;

    /**
     * The list of URL segments.
     *
     * @var array
     */
    protected $segments = [], $segmentsLeft = [];

    /**
     * Get the count of the total URL segments.
     *
     * @var int
     */
    protected $segmentsCount = 0, $segmentsLeftCount = 0;

    /**
     * The array of the Page instances.
     *
     * @var array
     */
    protected $pages = [];

    /**
     * The array of the attached types of the Page.
     *
     * @var array
     */
    protected $attachedTypes = [];

    /**
     * The array of the implicit types of the Page.
     *
     * @var array
     */
    protected $implicitTypes = [];

    /**
     * The array of the modules type of the Page.
     *
     * @var array
     */
    protected $moduleTypes = [];

    /**
     * The array of the types with an additional URIs.
     *
     * @var array
     */
    protected $tabs = [];

    /**
     * The array of the types that will allow specific requests.
     *
     * @var array
     */
    protected $requestMethods = [];

    /**
     * Define a dynamic routes.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function ($app) {
            $this->config = $app['config'];

            if (! $this->config->get('cms_is_booted')) {
                $this->request = $app['request'];

                $this->router = $app['router'];

                if ($this->config->get('language_isset')) {
                    $this->uriPrefix = $this->config->get('app.language') . '/';
                }

                $routeMatches = 0;

                foreach ($this->router->getRoutes()->get($this->request->method()) as $route) {
                    if ($route->matches($this->request)) {
                        $routeMatches = 1;

                        break;
                    }
                }

                if (! $routeMatches) {
                    $this->build();
                }
            }
        });
    }

    /**
     * Set router configuration.
     *
     * @return void
     */
    protected function configure()
    {
        $this->segments = (array) $this->config->get('url_segments');

        $this->segmentsCount = $this->config->get('url_segments_count', 0);

        $this->attachedTypes = (array) $this->config->get('cms.pages.attached');

        $this->implicitTypes = (array) $this->config->get('cms.pages.implicit');

        $this->requestMethods = (array) $this->config->get('cms.methods');

        $this->moduleTypes = (array) $this->config->get('cms.modules');

        $this->tabs = (array) $this->config->get('cms.tabs');
    }

    /**
     * Build a new routes.
     *
     * @return void
     */
    public function build()
    {
        $this->configure();

        $this->router->group([
            'middleware' => 'web',
            'namespace' => $this->namespace
        ], function () {
            $this->setRoutes();
        });
    }

    /**
     * Set the specific route by URL segments.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function setRoutes()
    {
        if (! $this->segmentsCount) {
            $this->router->get($this->uriPrefix, [
                'uses' => 'SiteHomeController@index'
            ]);

            return;
        }

        $parentId = 0;

        for ($i = 0; $i < $this->segmentsCount; $i++) {
            $page = (new Page)->route($this->segments[$i], $parentId)->first();

            if (is_null($page)) {
                if (count($this->pages) < 1
                    || ! in_array($this->pages[$i - 1]->type, $this->attachedTypes)
                ) {
                    $this->app->abort(404);
                }

                break;
            }

            $page->original_slug = $page->slug;

            if ($i > 0) {
                $page->parent_slug = $this->pages[$i - 1]->slug;

                $page->slug = $page->parent_slug . '/' . $page->slug;
            }

            $parentId = $page->id;

            $this->pages[$i] = $page;
        }

        $this->detectRoutes();
    }

    /**
     * Detect the route by URL segments.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function detectRoutes()
    {
        $page = end($this->pages) or $this->app->abort(404);

        $this->pagesCount = count($this->pages);

        if (($this->pagesCount == $this->segmentsCount)
            && ! in_array($page->type, $this->implicitTypes)
        ) {
            $this->setCurrentRoute($page->type, [$page], 'index');

            return;
        }

        $this->segmentsLeft = array_slice($this->segments, $this->pagesCount);

        if (($this->segmentsLeftCount = count($this->segmentsLeft)) > 2) {
            $this->app->abort(404);
        }

        $this->setAttachedTypeRoute($page);
    }

    /**
     * Set the route by the attached type.
     *
     * @param  \Models\Page  $page
     * @return void
     */
    protected function setAttachedTypeRoute(Page $page)
    {
        $slug = current($this->segmentsLeft);

        if (in_array($page->type, $this->moduleTypes)) {
            $this->setCurrentRoute($page->type, [$page, $slug], 'show');

            return;
        }

        $implicitModel = model_path($page->type);

        $implicitModel = (new $implicitModel)->findOrFail($page->type_id);

        if (! $slug) {
            $this->setCurrentRoute($implicitModel->type, [
                $page, $implicitModel
            ], 'index', $this->pagesCount);

            return;
        }

        if (! in_array($implicitModel->type, $this->implicitTypes)) {
            $this->setCurrentRoute($implicitModel->type, [$page, $slug], 'show');

            return;
        }

        $this->setInnerAttachedTypeRoute(
            $implicitModel->type, $implicitModel->id, $slug
        );
    }

    /**
     * Set the route by the inner attached type.
     *
     * @param  string  $type
     * @param  int     $id
     * @param  string  $slug
     * @return void
     */
    protected function setInnerAttachedTypeRoute($type, $id, $slug)
    {
        $model = model_path($type);

        $model = (new $model);

        if (method_exists($model, 'bySlug')) {
            $model = $model->bySlug($slug, $id)->firstOrFail();
        } else {
            $model = $model->findOrFail($id);
        }

        $this->setCurrentRoute($model->type, [$model], 'index');
    }

    /**
     * Set the current route.
     *
     * @param  string  $type
     * @param  array   $parameters
     * @param  string|null  $defaultMethod
     * @param  int  $fakeBind
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function setCurrentRoute($type, array $parameters = [], $defaultMethod = null, $fakeBind = 0)
    {
        if ($this->segmentsLeftCount == 2) {
            if (array_key_exists($type, $this->tabs)
                && (array_key_exists(
                        $tabKey = $tab = (string) end($this->segmentsLeft),
                        $tabs = (array) $this->tabs[$type]
                    )
                    || is_int($tabKey = key($tabs))
                )
            ) {
                $defaultMethod = $this->tabs[$type][$tabKey];

                $parameters[] = $tab;
            } else {
                $this->app->abort(404);
            }
        }

        $typeParts = explode('@', $type);

        $controller = $this->getControllerPath($typeParts[0]);

        $method = count($typeParts) == 2 ? $typeParts[1] : $defaultMethod;

        $segments = '';

        $paramsCount = count($parameters);

        for ($i = 0; $i <= ($this->segmentsCount - ($paramsCount + 1)); $i++) { 
            $segments .= $this->segments[$i] . '/';
        }

        foreach ($parameters as $key => $binder) {
            $segments .= '{bind' . $key . '}' . (
                ($paramsCount - $fakeBind - 1) == $key ? '' : '/'
            );

            $key = 'bind' . $key;

            $this->router->bind($key, function () use ($binder) {
                return $binder;
            });
        }

        $this->app->instance('breadcrumb', new Collection($this->pages));

        $route = strtolower($this->request->method());

        if (array_key_exists($route, $this->requestMethods)
            && array_key_exists(
                $type = "{$typeParts[0]}@{$method}",
                $types = $this->requestMethods[$route]
            )
        ) {
            $method = $types[$type];
        } else {
            $route = 'get';
        }

        $this->router->{$route}($this->uriPrefix . $segments, [
            'uses' => $controller . '@' . $method
        ]);
    }

    /**
     * Get the controller path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getControllerPath($path)
    {
        $namespace = '';

        $path = explode('.', $path);

        if (($pathCount = count($path)) > 1) {
            for ($i = 1; $i <= $pathCount; $i++) {
                if ($i == $pathCount) {
                    $namespace .= '\\' . studly_case($path[$i - 2])
                                       . studly_case($path[$i - 1]);
                } else {
                    $namespace .= '\\' . studly_case($path[$i - 1]);
                }
            }
        } else {
            $namespace .= 'Site' . studly_case($path[0]);
        }

        return ltrim($namespace .= 'Controller', '\\');
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
