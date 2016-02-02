<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class SiteHomeController extends Controller
{
    /**
     * Display a home page.
     *
     * @return Response
     */
    public function index()
    {
        return view('site.home');
    }
}
