<?php

namespace App\Http\Controllers\Admin;

use Models\Collection;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CollectionRequest;

class AdminCollectionsController extends Controller
{
    /**
     * The Collection instance.
     *
     * @var \Models\Collection
     */
    protected $model;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Collection  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Collection $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;

    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data['items'] = $this->model->get();

        return view('admin.collections.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['current'] = $this->model;
        $data['current']->type = $this->request->get('type');
        $data['current']->admin_per_page = 20;
        $data['current']->site_per_page = 10;

        return view('admin.collections.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CollectionRequest  $request
     * @return Response
     */
    public function store(CollectionRequest $request)
    {
        $input = $request->all();

        $newModel = $this->model->create($input);

        if ($request->has('close')) {
            return redirect()->route(cms_route('collections.index'))
                                ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('collections.edit', [$newModel->id]))
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['current'] = $this->model->findOrFail($id);

        return view('admin.collections.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CollectionRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CollectionRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('collections.index'))
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
        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
