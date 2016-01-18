<?php

namespace App\Http\Controllers\Admin;

use Models\CmsUser;
use Custom\Auth\Auth;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CmsUserRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminCmsUsersController extends Controller
{
    /**
     * The CmsUser instance.
     *
     * @var \Models\CmsUser
     */
    protected $model;

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
     * Create a new controller instance.
     *
     * @param  \Models\CmsUser  $model
     * @param  \Illuminate\Http\Request  $request
     * @param  \Custom\Auth\Auth  $auth
     * @return void
     */
    public function __construct(CmsUser $model, Request $request, Auth $auth)
    {
        $this->model = $model;

        $this->request = $request;

        $this->auth = $auth->cms();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $model = $this->model;

        if ($this->request->has('role')) {
            $model = $model->where('role', $this->request->get('role'));
        }

        $data['items'] = $model->orderDesc()->paginate(20);

        return view('admin.cms_users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (! $this->auth->get()->isAdmin()) {
            throw new AccessDeniedHttpException;
        }

        $data['current'] = $this->model;

        $data['roles'] = user_roles();

        return view('admin.cms_users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CmsUserRequest  $request
     * @return Response
     */
    public function store(CmsUserRequest $request)
    {
        if (! $this->auth->get()->isAdmin()) {
            throw new AccessDeniedHttpException;
        }

        $input = $request->all();

        $newModel = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('cmsUsers.index'))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('cmsUsers.edit', [$newModel->id]))
                ->with('alert', fill_data('success', trans('general.created')));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $data['current'] = $this->model->findOrFail($id);

        return view('admin.cms_users.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (! $this->auth->get()->isAdmin() && $this->auth->id() != $id) {
            return redirect()->back();
        }

        $data['current'] = $this->model->findOrFail($id);

        $data['roles'] = user_roles();

        return view('admin.cms_users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CmsUserRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CmsUserRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('cmsUsers.index'))
                    ->with('alert', fill_data('success', trans('general.updated')));
        }

        return redirect()->back()->with('alert', fill_data('success', trans('general.updated')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if ($this->auth->get()->isAdmin()) {
            $user = $this->model->findOrFail($id);

            if ($this->auth->id() == $id) {
                $this->model = null;
            }
        } else {
            $this->model = null;
        }

        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
