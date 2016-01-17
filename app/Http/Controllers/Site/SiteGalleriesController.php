<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Gallery;
use Models\Collection;
use App\Http\Controllers\Controller;

class SiteGalleriesController extends Controller
{
    /**
     * The Gallery instance.
     *
     * @var \Models\Gallery
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Gallery  $model
     * @return void
     */
    public function __construct(Gallery $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the gallery.
     *
     * @param  \Models\Page  $page
     * @param  \Models\Collection  $collection
     * @return Response
     */
    public function index(Page $page, Collection $collection)
    {
        $data['current'] = $page;

        $data['items'] = $this->model->getSiteCollection($collection);

        return view('site.gallery', $data);
    }
}
