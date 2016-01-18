<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Catalog;
use Models\Collection;
use App\Http\Controllers\Controller;

class SiteCatalogController extends Controller
{
    /**
     * The Catalog instance.
     *
     * @var \Models\Catalog
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Catalog  $model
     * @return void
     */
    public function __construct(Catalog $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the catalog.
     *
     * @param  \Models\Page  $page
     * @param  \Models\Collection  $collection
     * @return Response
     */
    public function index(Page $page, Collection $collection)
    {
        $data['current'] = $page;

        $data['items'] = $this->model->getSiteCollection($collection);

        return view('site.catalog', $data);
    }

    /**
     * Display the specified catalog.
     *
     * @param  \Models\Page  $page
     * @param  string  $slug
     * @return Response
     */
    public function show(Page $page, $slug)
    {
        $data['parent'] = $page;

        $data['current'] = $this->model->bySlug($slug)->firstOrFail();

        $data['files'] = $this->model->getFiles($data['current']->id);

        return view('site.catalog_item', $data);
    }
}
