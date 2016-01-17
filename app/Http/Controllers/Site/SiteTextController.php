<?php

namespace App\Http\Controllers\Site;

use Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SiteTextController extends Controller
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display the specified page.
     *
     * @param  \Models\Page  $page
     * @return Response
     */
    public function index(Page $page)
    {
        $data['current'] = $page;

        $data['files'] = $page->getFiles($page->id);

        return view('site.text', $data);
    }
}
