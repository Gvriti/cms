<?php

namespace App\Providers\Models;

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
        // Do not boot if we are running in the console to avoid migration fail.
        // Do not boot if CMS will not load.
        if (! $this->app->runningInConsole() && cms_will_load()) {
            $start = date('Y-m-d');
            $end = date('Y-m-d', strtotime('+7 days', strtotime($start)));

            $calendar = (new Calendar)->getActive($start, $end);

            view()->composer([
                'admin.partials.user',
                'admin.partials.horizontal_menu',
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
