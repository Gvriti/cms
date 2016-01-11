<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Collection;
use Models\Abstracts\Model;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection as Collect;
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
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

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
     * The Page instance.
     *
     * @var \Models\Page
     */
    protected $page;

    /**
     * The Page instances.
     *
     * @var array
     */
    protected $pages = [];

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;

        $this->request = $request;

        $this->page = new Page;
    }

    /**
     * Run the site controller.
     *
     * @return \Illuminate\Routing\Controller
     */
    public function run()
    {
        $this->segments = func_get_args();

        $this->segmentsCount = count($this->segments);

        return $this->getController();
    }

    /**
     * Get specific controller by URL segments.
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
            $page = $this->page->route($this->segments[$i], $parentId)->first();

            if (is_null($page)) {
                if (count($this->pages) < 1 || $this->pages[$i - 1]->type != 'collection') {
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
     * Detect controller by URL segments.
     *
     * @return \Illuminate\Routing\Controller
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function detectController()
    {
        $page = end($this->pages) or $this->app->abort(404);

        $pagesCount = count($this->pages);

        if ($pagesCount == $this->segmentsCount && $page->type != 'collection') {
            return $this->callController($page->type, ['page' => $page], 'show');
        }

        $this->segments = array_slice($this->segments, $pagesCount);

        if (($this->segmentsCount = count($this->segments)) > 1) {
            $this->app->abort(404);
        }

        return $this->getCollectionTypeController($page);
    }

    /**
     * Get a controller by the collection type.
     *
     * @param  \Models\Page  $page
     * @return \Illuminate\Routing\Controller
     */
    protected function getCollectionTypeController(Page $page)
    {
        $collection = (new Collection)->findOrFail($page->collection_id);

        if (! $this->segmentsCount) {
            return $this->callController($collection->type, [
                'page' => $page,
                'collection' => $collection
            ], 'index');
        }

        $slug = current($this->segments);

        if (! in_array($collection->type, double_collection())) {
            return $this->callController($collection->type, [
                'page' => $page,
                'slug' => $slug
            ], 'show');
        }
        return $this->getDoubleCollectionTypeController($collection, $slug);
    }

    /**
     * Get a controller by the double collection.
     *
     * @param  \Models\Collection  $collection
     * @param  string  $slug
     * @return \Illuminate\Routing\Controller
     */
    protected function getDoubleCollectionTypeController(Collection $collection, $slug)
    {
        $modelName = $this->getModelName($collection->type);

        $model = (new $modelName)->bySlug($slug, $collection->id)->firstOrFail();

        return $this->callController($model->type, [
            str_singular($model->getTable()) => $model
        ], 'index');
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
     * Get the evaluated view contents.
     *
     * @param  \Illuminate\Contracts\View\View  $view
     * @return \Illuminate\Contracts\View\View
     */
    protected function view(View $view)
    {
        if ($view->current instanceof Model
            && ! $view->current instanceof Page
            && $this->pages) {
            $lastSlug = end($this->pages)->slug;

            $view->current->original_slug = $view->current->slug;

            $view->current->slug = $lastSlug . '/' . $view->current->slug;

            $this->pages[] = $view->current;
        }

        $this->createBreadcrumb($this->pages);

        return $view;
    }

    /**
     * Create a breadcrumb.
     *
     * @param  array  $items
     * @return void
     */
    protected function createBreadcrumb($items)
    {
        $this->app->instance('breadcrumb', new Collect($items));
    }
}
