<?php

namespace App\Http\Controllers\Admin;

use Models\Gallery;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;

class AdminGalleriesController extends Controller
{
    use VisibilityTrait, PositionableTrait, MovableTrait;

    /**
     * The Gallery instance.
     *
     * @var \Models\Gallery
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
     * @param  \Models\Gallery  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Gallery $model, Request $request)
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

        $data['items'] = $model->getAdminCollection($data['collection']);

        $data['similarTypes'] = $model->byType()->get();

        return view('admin.galleries.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $collectionId
     * @return Response
     */
    public function create($collectionId)
    {
        $data['current'] = $this->model;
        $data['current']->collection_id = $collectionId;
        $data['current']->type = $this->request->get('type');
        $data['current']->admin_per_page = 20;
        $data['current']->site_per_page = 10;

        return view('admin.galleries.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\GalleryRequest  $request
     * @param  int  $collectionId
     * @return Response
     */
    public function store(GalleryRequest $request, $collectionId)
    {
        $input = $request->all();
        $input['collection_id'] = $collectionId;

        $newModel = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('galleries.index', [$collectionId]))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('galleries.edit', [$collectionId, $newModel->id]))
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
        $data['items'] = $this->model->joinLanguages(false)
                                     ->where('id', $id)
                                     ->get();

        return view('admin.galleries.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\GalleryRequest  $request
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function update(GalleryRequest $request, $collectionId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('galleries.index', [$collectionId]))
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
            new AdminDestroy($this->model, $id, false)
        );
    }
}
