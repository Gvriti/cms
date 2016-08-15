<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use App\Http\Controllers\Controller;

class SiteSearchController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \Models\Page  $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Page $page)
    {
        $data['current'] = $page;

        // do whatever you want

        return view('site.search', $data);
    }
}
