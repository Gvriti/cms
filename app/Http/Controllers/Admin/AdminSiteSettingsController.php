<?php

namespace App\Http\Controllers\Admin;

use DB;
use Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSiteSettingsController extends Controller
{
    /**
     * Display a settings list.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data['siteSettings'] = DB::table('site_settings')->first();

        return view('admin.site_settings.index', $data);
    }

    /**
     * Update the `site_settings` table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $columns = array_flip(Schema::getColumnListing('site_settings'));
        unset($columns['id']);

        $attributes = $request->all();

        $attributes = array_intersect_key($attributes, $columns);

        DB::table('site_settings')->update($attributes);

        return redirect(cms_route('siteSettings.index'))
                    ->with('alert', fill_data('success', trans('general.updated')));
    }
}
