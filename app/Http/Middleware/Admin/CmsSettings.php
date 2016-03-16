<?php

namespace App\Http\Middleware\Admin;

use Auth;
use Closure;
use Illuminate\Support\Collection;

class CmsSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $settings = app('db')->table('cms_settings')->where('cms_user_id', Auth::guard('cms')->id())->first();

        if (! is_null($settings)) {
            $settings->body = "$settings->sidebar_direction $settings->layout_boxed $settings->skin_sidebar $settings->skin_user_menu $settings->skin_horizontal";
            $settings->body = preg_replace('/\s+/', ' ', trim($settings->body));
        }

        $settings = new Collection($settings);

        view()->share(['settings' => $settings]);

        return $next($request);
    }
}
