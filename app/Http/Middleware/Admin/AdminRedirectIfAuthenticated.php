<?php

namespace App\Http\Middleware\Admin;

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
        $auth = $request->user()->cms();

        if ($auth->check() && ! $auth->get()->hasLockScreen()) {
            if ($request->ajax()) {
                return response()->json(fill_data(true));
            }

            return redirect(cms_route('dashboard'));
        }

        return $next($request);
    }
}
