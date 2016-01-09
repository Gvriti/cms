<?php

namespace App\Http\Controllers\Admin;

use DB;
use Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminSettingsController extends Controller
{
    /**
     * Display a settings list.
     *
     * @return Response
     */
    public function index()
    {
        $data = [
            'sidebarDirection' => [
                'left-sidebar'  => 'Left',
                'right-sidebar' => 'Right'
            ],
            'sidebarPosition' => [
                'fixed'            => 'Fixed',
                'fixed collapsed'  => 'Fixed & Collapsed',
                'static'           => 'Static',
                'static collapsed' => 'Static & Collapsed'
            ],
            'alertPosition' => [
                'top-right'         => 'Top Right',
                'top-left'          => 'Top Left',
                'top-center'        => 'Top Center',
                'top-full-width'    => 'Top Full Width',
                'bottom-right'      => 'Bottom Right',
                'bottom-left'       => 'Bottom Left',
                'bottom-center'     => 'Bottom Center',
                'bottom-full-width' => 'Bottom Full Width'
            ],
            'lockscreen' => [
                '0'       => 'Disable',
                '30000'   => '30 Seconds',
                '60000'   => '1 Minute',
                '300000'  => '5 Minutes',
                '600000'  => '10 Minutes',
                '1200000' => '20 Minutes',
                '1800000' => '30 Minutes',
                '3600000' => '1 Hour'
            ]
        ];

        return view('admin.cms_settings.index', $data);
    }

    /**
     * Update the `cms_settings` table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $columns = array_flip(Schema::getColumnListing('cms_settings'));
        unset($columns['id']);

        $attributes = $request->all();
        $checkboxes = [
            'ajax_form'               => 'ajax-form',
            'layout_boxed'            => 'boxed-container',
            'horizontal_menu_click'   => 'click-to-expand',
            'horizontal_menu_minimal' => 'navbar-minimal'
        ];

        foreach ($checkboxes as $key => $value) {
            if (isset($attributes[$key])) {
                $attributes[$key] = $value;
            } else {
                $attributes[$key] = null;
            }
        }

        $attributes['horizontal_menu'] = $request->has('horizontal_menu') ? 1 : 0;

        $attributes = array_intersect_key($attributes, $columns);

        DB::table('cms_settings')->update($attributes);

        return redirect(cms_route('settings.index', ['tab' => $request->get('tab', 1)]))
                    ->with('alert', fill_data('success', trans('general.updated')));
    }
}
