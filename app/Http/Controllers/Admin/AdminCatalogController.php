<?php

namespace App\Http\Controllers\Admin;

use Models\Catalog;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatalogRequest;

class AdminCatalogController extends Controller
{
    use VisibilityTrait, PositionableTrait, MovableTrait;

    /**
     * The Catalog instance.
     *
     * @var \Models\Catalog
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
     * @param  \Models\Catalog  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Catalog $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $collectionId
     * @return Response
     */
    public function index($collectionId)
    {
        $model = $this->model;

        $data['collection'] = $model->collection()->findOrFail($collectionId);

        $data['items'] = $model->joinFileId()->getAdminCollection($data['collection']);

        $data['similarTypes'] = $model->byType()->get();

        return view('admin.catalog.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $collectionId
     * @return Response
     */
    public function create($collectionId)
    {
        $data['collectionId'] = $collectionId;

        return view('admin.catalog.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CatalogRequest $request
     * @param  int  $collectionId
     * @return Response
     */
    public function store(CatalogRequest $request, $collectionId)
    {
        $input = $request->all();
        $input['collection_id'] = $collectionId;

        $newModel = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('catalog.index', [$collectionId]))
                        ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('catalog.edit', [$collectionId, $newModel->id]))
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
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function edit($collectionId, $id)
    {
        $model = $this->model;

        $data['items'] = $model->joinLanguages()->where('id', $id)->getOrFail();

        return view('admin.catalog.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\CatalogRequest  $request
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function update(CatalogRequest $request, $collectionId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('catalog.index', [$collectionId]))
                    ->with('alert', fill_data('success', trans('general.updated')));
        }

        return redirect()->back()->with('alert', fill_data('success', trans('general.updated')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function destroy($collectionId, $id)
    {
        return $this->dispatch(
            new AdminDestroy($this->model, $id)
        );
    }
}
