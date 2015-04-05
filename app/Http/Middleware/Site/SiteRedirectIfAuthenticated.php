<?php

namespace App\Http\Middleware\Site;

use Closure;

class SiteRedirectIfAuthenticated
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
        $auth = $request->user()->user();

        if ($auth->check()) {
            return redirect(site_url());
        }

        return $next($request);
    }
}
