<?php

namespace App\Providers\Site;

use Exception;
use Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Config\Repository as Config;

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
     * @var \Illuminate\Config\Repository
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
     * Define a dynamic routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Config\Repository  $config
     * @return void
     */
    public function boot(Request $request, Router $router, Config $config)
    {
        $this->request = $request;

        $this->router = $router;

        $this->config = $config;

        $routes = $router->getRoutes()->getRoutes();

        if (language_isset()) {
            $this->uriPrefix = language() . '/';
        }

        try {
            $router->getRoutes()->match($this->request);

            $hasStaticRoute = true;
        } catch (Exception $e) {
            $hasStaticRoute = false;
        }

        if (! $config['cms_will_load'] && ! $hasStaticRoute) {
            $this->build();
        }
    }

    /**
     * Initialize routes properties.
     *
     * @return void
     */
    protected function init()
    {
        $this->segments = $this->config->get('url_segments', []);

        $this->segmentsCount = $this->config->get('url_segments_count', 0);

        $this->attachedTypes = $this->config->get('cms.pages.attached', []);

        $this->implicitTypes = $this->config->get('cms.pages.implicit', []);

        $this->moduleTypes = $this->config->get('cms.modules', []);

        $this->tabs = $this->config->get('cms.tabs', []);
    }

    /**
     * Build a new routes.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @return void
     */
    public function build()
    {
        $this->init();

        $this->router->group(['namespace' => $this->namespace], function () {
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

        $implicitModel = get_model_name($page->type);

        $implicitModel = (new $implicitModel)->findOrFail(
            $page->{str_singular($page->type) . '_id'}
        );

        if (! $slug) {
            $this->setCurrentRoute($implicitModel->type, [
                $page, $implicitModel
            ], 'index');

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
        $model = get_model_name($type);

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
     * @return void
     */
    protected function setCurrentRoute($type, array $parameters = [], $defaultMethod = null)
    {
        if ($this->segmentsLeftCount == 2) {
            if (array_key_exists($type, $this->tabs)
                && array_key_exists(
                    $tab = end($this->segmentsLeft), (array) $this->tabs[$type]
                )
            ) {
                $defaultMethod = $this->tabs[$type][$tab];

                $parameters[] = $tab;
            } else {
                $this->app->abort(404);
            }
        }

        $typeParts = explode('@', $type);

        $controller = $this->getControllerPath($typeParts[0]);

        $method = count($typeParts) == 2 ? $typeParts[1] : $defaultMethod;

        $segments = '';

        for ($i = 0; $i <= ($this->segmentsCount - 2); $i++) { 
            $segments .= $this->segments[$i] . '/';
        }

        foreach ($parameters as $key => $value) {
            $bindCallback = function () use ($value) {
                return $value;
            };

            if ($key < 1) {
                $segments .= '{slug}';

                $bindKey = 'slug';
            } else {
                $segments .= '{fake' . $key . '}';

                $bindKey = 'fake' . $key;
            }

            $this->router->bind($bindKey, $bindCallback);
        }

        $this->app->instance('breadcrumb', new Collection($this->pages));

        if ($this->request->method() == 'POST'
            && array_key_exists(
                $type = "{$typeParts[0]}@{$method}",
                $postTypes = $this->config->get('cms.post_methods', [])
            )
        ) {
            $route = 'post';

            $method = $postTypes[$type];
        } else {
            $route = 'get';
        }

        $this->router->{$route}($this->uriPrefix . $segments, [
            'as' => 'current', 'uses' => $controller . '@' . $method]
        );
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
            for ($i = 0; $i < $pathCount; $i++) {
                if (($i + 1) == $pathCount) {
                    $namespace .= 'Site' . studly_case($path[$i]);
                } else {
                    $namespace .= '\\' . studly_case($path[$i]);
                }
            }
        } else {
            $namespace .= 'Site' . studly_case($path[0]);
        }

        return $namespace .= 'Controller';
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
