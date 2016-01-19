<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Abstracts\Model;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;

class SiteController extends Controller
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

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
     * The array of the not showable types of the Page.
     *
     * @var array
     */
    protected $notShowableTypes = [];

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
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->config = $app['config'];
    }

    /**
     * Initialize site controller properties.
     *
     * @param  array  $segments
     * @return void
     */
    protected function init($segments)
    {
        $this->segments = $segments;

        $this->segmentsCount = count($segments);

        $this->notShowableTypes = (array) $this->config['cms.pages.noshow'];

        $this->attachedTypes = (array) $this->config['cms.pages.attached'];

        $this->implicitTypes = (array) $this->config['cms.pages.implicit'];

        $this->moduleTypes = (array) $this->config['cms.modules'];
    }

    /**
     * Build a new site controller instance.
     *
     * @return \Illuminate\Routing\Controller
     */
    public function build()
    {
        $this->init(func_get_args());

        return $this->getController();
    }

    /**
     * Get the specific controller by URL segments.
     *
     * @return \Illuminate\Routing\Controller
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getController()
    {
        if (! $this->segmentsCount) {
            return $this->app->call([$this->app[SiteHomeController::class], 'index']);
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

        return $this->view($this->detectController());
    }

    /**
     * Detect the controller by URL segments.
     *
     * @return \Illuminate\Routing\Controller
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function detectController()
    {
        $page = end($this->pages) or $this->app->abort(404);

        $pagesCount = count($this->pages);

        if (($pagesCount == $this->segmentsCount)
            && ! in_array($page->type, $this->implicitTypes)
        ) {
            return $this->callController($page->type, ['page' => $page], 'index');
        }

        $this->segments = array_slice($this->segments, $pagesCount);

        if (($this->segmentsCount = count($this->segments)) < 2) {
            return $this->getAttachedTypeController($page);
        }

        $this->app->abort(404);
    }

    /**
     * Get the controller by the attached type.
     *
     * @param  \Models\Page  $page
     * @return \Illuminate\Routing\Controller
     */
    protected function getAttachedTypeController(Page $page)
    {
        $slug = current($this->segments);

        if (in_array($page->type, $this->moduleTypes)) {
            return $this->getShowableController($page, $page->type, $slug);
        }

        $implicitModel = get_model_name($page->type);

        $implicitKey = str_singular($page->type);

        $implicitModel = (new $implicitModel)->findOrFail(
            $page->{$implicitKey . '_id'}
        );

        if (! $this->segmentsCount) {
            return $this->callController($implicitModel->type, [
                'page' => $page,
                $implicitKey => $implicitModel
            ], 'index');
        }

        if (! in_array($implicitModel->type, $this->implicitTypes)) {
            return $this->getShowableController($page, $implicitModel->type, $slug);
        }

        return $this->getInnerAttachedTypeController(
            $implicitModel->id, $implicitModel->type, $slug
        );
    }

    /**
     * Get the controller by the inner attached type.
     *
     * @param  int     $id
     * @param  string  $type
     * @param  string  $slug
     * @return \Illuminate\Routing\Controller
     */
    protected function getInnerAttachedTypeController($id, $type, $slug)
    {
        $model = get_model_name($type);

        $model = (new $model);

        if (method_exists($model, 'bySlug')) {
            $model = $model->bySlug($slug, $id)->firstOrFail();
        } else {
            $model = $model->findOrFail($id);
        }

        return $this->callController($model->type, [
            str_singular($model->getTable()) => $model
        ], 'index');
    }

    /**
     * Get the showable controller.
     *
     * @param  Page    $page
     * @param  string  $type
     * @param  string  $slug
     * @return \App\Http\Controllers\Controller
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getShowableController(Page $page, $type, $slug)
    {
        if (in_array($type, $this->notShowableTypes)) {
            $this->app->abort(404);
        }

        return $this->callController($type, [
            'page' => $page,'slug' => $slug
        ], 'show');
    }

    /**
     * Get the controller instance.
     *
     * @param  string  $name
     * @param  array   $parameters
     * @param  string|null  $defaultMethod
     * @return \App\Http\Controllers\Controller
     */
    protected function callController($name, array $parameters = [], $defaultMethod = null)
    {
        $controller = __NAMESPACE__;

        $segments = explode('@', $name);

        $namespace = explode('.', $segments[0]);

        if (($dirsCount = count($namespace)) > 1) {
            for ($i = 0; $i < $dirsCount; $i++) {
                if (($i + 1) == $dirsCount) {
                    $controller .= '\Site' . studly_case($namespace[$i]);
                } else {
                    $controller .= '\\' . studly_case($namespace[$i]);
                }
            }
        } else {
            $controller .= '\Site' . studly_case($segments[0]);
        }

        $controller .= 'Controller';

        $method = count($segments) == 2 ? $segments[1] : $defaultMethod;

        return $this->app->call([$this->app[$controller], $method], $parameters);
    }

    /**
     * Modify the evaluated view contents.
     *
     * @param  mixed  $response
     * @return Response
     */
    protected function view($response)
    {
        if ($response instanceof View) {
            if ($response->current instanceof Model
                && ! $response->current instanceof Page
                && $this->pages
            ) {
                $lastSlug = end($this->pages)->slug;

                $response->current->original_slug = $response->current->slug;

                $response->current->slug = $lastSlug . '/' . $response->current->slug;

                $this->pages[] = $response->current;
            }

            $this->createBreadcrumb($this->pages);
        }

        return $response;
    }

    /**
     * Create a breadcrumb.
     *
     * @param  array  $items
     * @return void
     */
    protected function createBreadcrumb($items)
    {
        $this->app->instance('breadcrumb', new Collection($items));
    }
}
