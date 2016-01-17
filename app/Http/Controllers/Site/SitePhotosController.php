<?php

namespace App\Http\Controllers\Site;

use Models\Photo;
use Models\Gallery;
use App\Http\Controllers\Controller;

class SitePhotosController extends Controller
{
    /**
     * The Photo instance.
     *
     * @var \Models\Photo
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Photo  $model
     * @return void
     */
    public function __construct(Photo $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the photo.
     *
     * @param  \Models\Gallery  $gallery
     * @return Response
     */
    public function index(Gallery $gallery)
    {
        $data['current'] = $gallery;

        $data['items'] = $this->model->getSiteGallery($gallery);

        return view('site.photos', $data);
    }
}
