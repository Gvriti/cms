<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Models\Article;
use Models\Collection;
use App\Http\Controllers\Controller;

class SiteArticlesController extends Controller
{
    /**
     * The Article instance.
     *
     * @var \Models\Article
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Article  $model
     * @return void
     */
    public function __construct(Article $model)
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

        return view('site.articles', $data);
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

        $data['files'] = $this->model->getFiles($data['current']->id);

        return view('site.article', $data);
    }
}
