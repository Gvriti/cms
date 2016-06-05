<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use App\Http\Controllers\Controller;

class SiteTextController extends Controller
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

        $data['files'] = $page->getFiles($page->id);

        return view('site.text', $data);
    }
}
