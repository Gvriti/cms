<?php

namespace App\Http\Middleware\Admin;

use Auth;
use Closure;

class AdminRedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $guard = Auth::guard('cms');

        if ($guard->check() && ! $guard->user()->hasLockScreen()) {
            if ($request->ajax()) {
                return response()->json(fill_data(true));
            }

            return redirect(cms_route('dashboard'));
        }

        return $next($request);
    }
}
