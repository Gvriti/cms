<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Models\Catalog;
use Models\Collection;
use Models\Page;

class WebCatalogController extends Controller
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
     * Display a listing of the resource.
     *
     * @param  \Models\Page  $page
     * @param  \Models\Collection  $collection
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Page $page, Collection $collection)
    {
        $data['current'] = $page;

        $data['items'] = $this->model->getPublicCollection($collection);

        return view('web.catalog', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Models\Page  $page
     * @param  string  $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Page $page, $slug)
    {
        $data['parent'] = $page;

        $data['current'] = $this->model->bySlug($slug)->firstOrFail();

        $data['files'] = $data['current']->getFiles();

        return view('web.catalog_item', $data);
    }
}
