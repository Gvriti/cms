<?php

namespace App\Providers\Admin;

use Models\Calendar;
use Illuminate\Support\ServiceProvider;

class CalendarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Do not boot if running in console to avoid artisan fail, when db table doesn't exists.
        // Boot if CMS is booted.
        if (! $this->app->runningInConsole() && cms_is_booted()) {
            $start = date('Y-m-d');
            $end = date('Y-m-d', strtotime('+7 days', strtotime($start)));

            $calendar = (new Calendar)->getActive($start, $end);

            view()->composer([
                'admin._partials.user',
                'admin._partials.horizontal_menu',
                'admin.dashboard.index'
            ], function($view) use ($calendar) {
                $view->with('calendarEvents', $calendar);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
