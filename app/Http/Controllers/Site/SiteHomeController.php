<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class SiteHomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('site.home');
    }
}
