<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class SiteHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('site.home');
    }
}
