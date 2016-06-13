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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $guard = Auth::guard('cms');

        if ($guard->check() && ! $guard->user()->hasLockScreen()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(fill_data(true));
            }

            return redirect(cms_route('dashboard'));
        }

        return $next($request);
    }
}
