<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Models\Permission;
use Illuminate\Contracts\Auth\Guard;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminAuthenticate
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Auth\SessionGuard
     */
    protected $guard;

    /**
     * Create a new middleware instance.
     *
     * @param  \Models\Permission  $permission
     * @return void
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->guard->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(cms_route('login'));
            }
        }

        if (! $this->guard->user()->active) {
            throw new AccessDeniedHttpException;
        }

        if ($this->guard->user()->hasLockScreen()) {
            return redirect(cms_route('lockscreen'));
        }

        $this->checkRoutePermission($request);

        return $next($request);
    }

    /**
     * Determine if the user has access to the given route
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    private function checkRoutePermission($request)
    {
        if (! $this->guard->user()->isAdmin()) {
            $routeName = $request->route()->getName();

            if (! (new Permission)->permissions($this->guard->id())->accessRoute($routeName)) {
                throw new AccessDeniedHttpException;
            }
        }
    }
}
