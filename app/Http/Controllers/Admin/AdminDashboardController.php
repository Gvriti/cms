<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;

class AdminDashboardController extends Controller
{
    /**
     * Display a Dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $db = app('db');

        // count basic
        $data['menusTotal'] = $db->table('menus')->count();

        $data['pagesTotal'] = $db->table('pages')->count();
        $data['mainPage'] = $db->table('menus')->where('main', 1)->first();
        $data['mainPagesTotal'] = $db->table('pages')->where('menu_id',
            is_null($data['mainPage']) ? 1 : $data['mainPage']->id
        )->count();

        $data['collectionsTotal'] = $db->table('collections')->count();
        $data['usersTotal'] = $db->table('cms_users')->count();

        // count collection types
        $data['galleriesTotal'] = $db->table('collections')->where('type', 'galleries')->count();

        // catalog
        $data['catalogTotalDistinct'] = $db->table('catalog')->count($db->raw('DISTINCT collection_id'));
        $data['catalogTotal'] = $db->table('catalog')->count();

        // articles
        $data['articlesTotalDistinct'] = $db->table('articles')->count($db->raw('DISTINCT collection_id'));
        $data['articlesTotal'] = $db->table('articles')->count();

        // photos
        $data['photoAlbumTotal'] = $db->table('galleries')->where('type', 'photos')->count();
        $data['photosTotal'] = $db->table('photos')->count();

        // videos
        $data['videoAlbumTotal'] = $db->table('galleries')->where('type', 'videos')->count();
        $data['videosTotal'] = $db->table('videos')->count();

        // calendar
        $data['calendarTotal'] = $db->table('calendar')->count();

        // files
        $data['filesTotal'] = $db->table('files')->count();
        $data['filesTotalDistinct'] = $db->table('files')->count($db->raw('DISTINCT route_name'));

        // notes
        $data['notes'] = $db->table('notes')->orderBy('id', 'desc')->take(5)->get();

        return view('admin.dashboard.index', $data);
    }
}
