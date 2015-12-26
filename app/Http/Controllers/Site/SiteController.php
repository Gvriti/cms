<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Collection;
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
            $controller = $this->getControllerName($page->type);

            return $this->app->call([$this->app[$controller], 'show'], ['page' => $page]);
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

        $controller = $this->getControllerName($collection->type);

        if (! $this->segmentsCount) {
            return $this->app->call([$this->app[$controller], 'index'], [
                'page' => $page,
                'collection' => $collection
            ]);
        }

        $slug = current($this->segments);

        if (! in_array($collection->type, double_collection())) {
            return $this->app->call([$this->app[$controller], 'show'], [
                'page' => $page,
                'slug' => $slug
            ]);
        }
        return $this->getDoubleCollectionTypeController($collection->type, $slug);
    }

    /**
     * Get a controller by the double collection type.
     *
     * @param  string  $type
     * @param  string  $slug
     * @return \Illuminate\Routing\Controller
     */
    protected function getDoubleCollectionTypeController($type, $slug)
    {
        $modelName = $this->getModelName($type);

        $model = $this->app[$modelName]->bySlug($slug)->firstOrFail();

        $controller = $this->getControllerName($model->type);

        return $this->app->call([$this->app[$controller], 'index'], [
            str_singular($model->getTable()) => $model
        ]);
    }

    /**
     * Get the evaluated view contents.
     *
     * @param  \Illuminate\Contracts\View\View  $view
     * @return \Illuminate\Contracts\View\View
     */
    protected function view(View $view)
    {
        if (! $view->current instanceof Page && $this->pages) {
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

    /**
     * Get the controller name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getControllerName($name)
    {
        return __NAMESPACE__ . '\Site' . studly_case($name) . 'Controller';
    }
}
