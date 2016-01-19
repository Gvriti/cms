<?php

namespace App\Providers\Site;

use Exception;
use Models\Page;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Config\Repository as Config;

class DynamicRouteServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the dynamic site routes.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers\Site';

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
     * The current language of the application.
     *
     * @var string
     */
    protected $language;

    /**
     * The list of URL segments.
     *
     * @var array
     */
    protected $segments = [];

    /**
     * Get the count of the total URL segments.
     *
     * @var int
     */
    protected $segmentsCount = 0;

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
     * Define dynamic site routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Config\Repository  $config
     * @return void
     */
    public function boot(Router $router, Config $config)
    {
        $this->router = $router;

        $this->config = $config;

        $routes = $router->getRoutes()->getRoutes();

        if (language_isset()) {
            $this->language = language() . '/';
        }

        try {
            $router->getRoutes()->match($this->app['request']);

            $hasStaticRoute = true;
        } catch (Exception $e) {
            $hasStaticRoute = false;
        }

        if (! $config['cms_will_load'] && ! $hasStaticRoute) {
            $this->build();
        }
    }

    /**
     * Initialize site route properties.
     *
     * @return void
     */
    protected function init()
    {
        $this->segments = $this->config['url_segments'];

        $this->segmentsCount = $this->config['url_segments_count'];

        $this->attachedTypes = (array) $this->config['cms.pages.attached'];

        $this->implicitTypes = (array) $this->config['cms.pages.implicit'];

        $this->moduleTypes = (array) $this->config['cms.modules'];
    }

    /**
     * Build a new site router.
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
            $this->router->get($this->language, [
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
     * Detect routes by URL segments.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function detectRoutes()
    {
        $page = end($this->pages) or $this->app->abort(404);

        $pagesCount = count($this->pages);

        if (($pagesCount == $this->segmentsCount)
            && ! in_array($page->type, $this->implicitTypes)
        ) {
            $this->setCurrentRoute($page->type, [$page], 'index');

            return;
        }

        $segmentsLeft = array_slice($this->segments, $pagesCount);

        if (count($segmentsLeft) < 2) {
            $this->setAttachedTypeRoute($page, $segmentsLeft);

            return;
        }

        $this->app->abort(404);
    }

    /**
     * Set route by the attached type.
     *
     * @param  \Models\Page  $page
     * @param  array  $segments
     * @return void
     */
    protected function setAttachedTypeRoute(Page $page, array $segments)
    {
        $slug = current($segments);

        if (in_array($page->type, $this->moduleTypes)) {
            $this->setCurrentRoute($page->type, [$page, $slug], 'show');

            return;
        }

        $implicitModel = get_model_name($page->type);

        $implicitModel = (new $implicitModel)->findOrFail(
            $page->{str_singular($page->type) . '_id'}
        );

        if (! count($segments)) {
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
     * Set the routes.
     *
     * @param  string  $type
     * @param  array   $parameters
     * @param  string|null  $defaultMethod
     * @return void
     */
    protected function setCurrentRoute($type, array $parameters = [], $defaultMethod = null)
    {
        $type = explode('@', $type);

        $controller = $this->getControllerPath($type[0]);

        $method = count($type) == 2 ? $type[1] : $defaultMethod;

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

        $this->router->get($this->language . $segments, [
            'as' => 'current', 'uses' => $controller . '@' . $method]
        );
    }

    /**
     * Get the controller path.
     *
     * @param  string  $type
     * @return string
     */
    protected function getControllerPath($type)
    {
        $namespace = '';

        $path = explode('.', $type);

        if (($pathCount = count($path)) > 1) {
            for ($i = 0; $i < $pathCount; $i++) {
                if (($i + 1) == $pathCount) {
                    $namespace .= 'Site' . studly_case($path[$i]);
                } else {
                    $namespace .= '\\' . studly_case($path[$i]);
                }
            }
        } else {
            $namespace .= 'Site' . studly_case($type);
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
