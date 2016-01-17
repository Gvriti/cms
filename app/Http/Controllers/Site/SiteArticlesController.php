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
     * Display a listing of the article.
     *
     * @param  \Models\Page  $page
     * @param  \Models\Collection  $collection
     * @return Response
     */
    public function index(Page $page, Collection $collection)
    {
        $data['current'] = $page;

        $data['items'] = $this->model->getSiteCollection($collection);

        return view('site.articles', $data);
    }

    /**
     * Display the specified article.
     *
     * @param  \Models\Page  $page
     * @param  string  $slug
     * @return Response
     */
    public function show(Page $page, $slug)
    {
        $data['prevPage'] = $page;

        $data['current'] = $model = $this->model->bySlug($slug)->firstOrFail();

        $data['files'] = $model->getFiles($model->id);

        return view('site.article', $data);
    }
}
