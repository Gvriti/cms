<?php

namespace App\Http\Controllers\Admin;

use Models\CmsUser;
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
     * The authenticated cms user instance.
     *
     * @var \Models\CmsUser
     */
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\CmsUser  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(CmsUser $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;

        $this->user = $request->user('cms');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['items'] = $this->model->adminFilter($this->request)
                                     ->orderDesc()
                                     ->paginate(20);

        return view('admin.cms_users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (! $this->user->isAdmin()) {
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
        if (! $this->user->isAdmin()) {
            throw new AccessDeniedHttpException;
        }

        $model = $this->model->create($request->all());

        app('db')->table('cms_settings')->insert(['cms_user_id' => $model->id]);

        if ($request->has('close')) {
            return redirect(cms_route('cmsUsers.index'))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('cmsUsers.edit', [$model->id]))
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
        if (! $this->user->isAdmin() && $this->user->id != $id) {
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
        if ($this->user->isAdmin()) {
            $user = $this->model->findOrFail($id);

            if ($this->user->id == $id) {
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
