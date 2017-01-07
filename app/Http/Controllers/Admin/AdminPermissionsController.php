<?php

namespace App\Http\Controllers\Admin;

use Models\CmsUser;
use Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Exception\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminPermissionsController extends Controller
{
    /**
     * The Permission instance.
     *
     * @var \Models\Permission
     */
    protected $model;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Route group names that are hidden for list.
     *
     * @var array
     */
    protected $routeGroupsHidden = ['dashboard', 'login', 'logout', 'lockscreen'];

    /**
     * Route names that are hidden for list.
     *
     * @var array
     */
    protected $routeNamesHidden = [
        'cms.cmsUsers.create', 'cms.cmsUsers.store', 'cms.cmsUsers.edit', 'cms.cmsUsers.update', 'cms.cmsUsers.destroy'
    ];

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Permission  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Permission $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Models\CmsUser  $model
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(CmsUser $user, $id)
    {
        $this->checkAccess($id);

        $data['user'] = $user->findOrFail($id);

        $data['current'] = $this->model->permissions($id)
            ->get()
            ->pluck('route_name')
            ->toArray();

        $routeNames = array_diff_key(
            $this->getAllRouteNames(),
            array_flip($this->routeGroupsHidden)
        );

        ksort($routeNames);

        $data['routes'] = $routeNames;

        $data['namesDisallowed'] = $this->routeNamesHidden;

        return view('admin.permissions.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($id)
    {
        $this->checkAccess($id);

        $input = $this->request->get('permissions', []);

        app('db')->transaction(function() use ($input, $id) {
            $this->model->clear($id);

            $attributes = [];

            foreach ($input as $key => $value) {
                if (in_array(key($value), $this->routeGroupsHidden)){
                    continue;
                }

                $attributes['cms_user_id'] = $id;
                $attributes['route_name'] = current($value);

                $this->model->create($attributes);
            }
        });

        return redirect(cms_route('permissions.index', [$id]))
            ->with('alert', fill_data('success', trans('general.saved')));
    }

    /**
     * Determine if the user has access to the given route
     *
     * @param  int  $id
     * @return void
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException|
     *         Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function checkAccess($id)
    {
        $user = $this->request->user('cms');

        if ($user->id == $id) {
            throw new HttpResponseException(redirect()->back());
        }

        if (! $user->isAdmin()) {
            throw new AccessDeniedHttpException;
        }
    }

    /**
     * Get all cms route names.
     *
     * @return array
     */
    protected function getAllRouteNames()
    {
        $routes = app('router')->getRoutes();

        $routeNames = [];

        $prevRouteName = null;

        $cmsSlug = cms_slug();

        foreach ($routes as $key => $route) {
            $routeName = $route->getName();

            if (! is_null($routeName) && strpos($route->getPrefix(), $cmsSlug) !== false) {
                if ($prevRouteName == $routeName) continue;

                $baseRouteName = explode('.', substr($routeName, 0, strrpos($routeName, '.')));

                $routeNames[$baseRouteName[0]][] = $routeName;

                $prevRouteName = $routeName;
            }
        }

        return $routeNames;
    }
}
