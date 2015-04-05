<?php

namespace App\Http\Controllers\Admin;

use Models\CmsUser;
use Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
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
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The authenticated cms instance.
     *
     * @var \Custom\Auth\Auth
     */
    protected $auth;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Models\Permission  $model
     * @return void
     */
    public function __construct(Request $request, Application $app, Permission $model)
    {
        $this->app = $app;

        $this->model = $model;

        $this->request = $request;

        $this->auth = $request->user()->cms();
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Models\CmsUser  $model
     * @param  int  $id
     * @return Response
     */
    public function index(CmsUser $user, $id)
    {
        if ($this->auth->id() == $id) {
            return redirect()->back();
        }

        if (! $this->auth->isAdmin()) {
            throw new AccessDeniedHttpException;
        }

        $data['user'] = $user->findOrFail($id);

        $data['item'] = $this->model->permissions($id)->get()
                                                     ->lists('route_name')
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
     * @return Response
     */
    public function store($id)
    {
        if (! $this->auth->isAdmin()) {
            throw new AccessDeniedHttpException;
        }

        $input = $this->request->get('permissions', []);

        $this->app['db']->transaction(function() use ($input, $id) {
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
                ->with('alert', msg_result('success', 'general.saved'));
    }

    /**
     * Get all cms route names.
     *
     * @return array
     */
    protected function getAllRouteNames()
    {
        $routes = $this->app['router']->getRoutes();

        $routeNames = [];

        $prevRouteName = null;

        $cmsSlug = cms_slug();

        foreach ($routes as $key => $route) {
            $routeName = $route->getName();

            if (! is_null($routeName) && strpos($route->getPrefix(), $cmsSlug) !== false) {
                if ($prevRouteName == $routeName) continue;

                $baseRouteName = explode('.', substr($routeName, strpos($routeName, '.') + 1));

                $routeNames[$baseRouteName[0]][] = $routeName;

                $prevRouteName = $routeName;
            }
        }

        return $routeNames;
    }
}
