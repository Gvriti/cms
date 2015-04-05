<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Models\Permission;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminAuthenticate
{
    /**
     * The Auth implementation.
     *
     * @var \Custom\Auth\Auth
     */
    protected $auth;

    /**
     * The Permission instance.
     *
     * @var \Models\Permission
     */
    protected $permission;

    /**
     * Create a new middleware instance.
     *
     * @param  \Models\Permission  $permission
     * @return void
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
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
        $this->auth = $request->user()->cms();

        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(cms_route('login'));
            }
        }

        if ($this->auth->get()->hasLockScreen()) {
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
        if (! $this->auth->get()->isAdmin()) {
            $routeName = $request->route()->getName();

            if (! $this->permission->permissions($this->auth->id())->accessRoute($routeName)) {
                throw new AccessDeniedHttpException;
            }
        }
    }
}
