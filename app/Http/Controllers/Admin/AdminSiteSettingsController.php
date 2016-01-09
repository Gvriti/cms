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
     * @return Response
     */
    public function index()
    {
        $data['siteSettings'] = DB::table('site_settings')->first();

        $data['dateFormatStatic'] = ['d F Y', 'F d, Y', 'd M Y', 'M d, Y'];

        return view('admin.site_settings.index', $data);
    }

    /**
     * Update the `site_settings` table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $columns = array_flip(Schema::getColumnListing('site_settings'));
        unset($columns['id']);

        $attributes = $request->all();

        if (! $request->has('date_format')) {
            $attributes['date_format'] = $request->get('date_format_custom');
        }

        $attributes = array_intersect_key($attributes, $columns);

        DB::table('site_settings')->update($attributes);

        return redirect(cms_route('siteSettings.index'))
                    ->with('alert', fill_data('success', trans('general.updated')));
    }
}
