<?php

namespace App\Http\Controllers\Admin;

use Models\Photo;
use Models\Gallery;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PhotoRequest;

class AdminPhotosController extends Controller
{
    use VisibilityTrait, PositionableTrait;

    /**
     * The Photo instance.
     *
     * @var \Models\Photo
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
     * @param  \Models\Photo  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Photo $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $galleryId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($galleryId)
    {
        $model = $this->model;

        $data['parent'] = (new Gallery)->joinLanguages()->findOrFail($galleryId);

        $data['items'] = $model->getAdminGallery($data['parent']);

        $data['similarTypes'] = $model->byType()->get();

        return view('admin.photos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $galleryId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create($galleryId)
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            $data['current'] = $this->model;
            $data['current']['gallery_id'] = $galleryId;

            $view = view('admin.photos.create', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('photos.index', [$galleryId]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\PhotoRequest  $request
     * @param  int  $galleryId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(PhotoRequest $request, $galleryId)
    {
        $input = $request->all();
        $input['gallery_id'] = $galleryId;

        $model = $this->model->create($input);

        if ($request->ajax() || $request->wantsJson()) {
            $view = view('admin.photos.item', [
                'item' => $model,
                'itemLang' => $input
            ])->render();

            return response()->json(
                fill_data('success', trans('general.created'))
                + ['view' => preg_replace('/\s+/', ' ', trim($view))]
            );
        }

        return redirect(cms_route('photos.index', [$galleryId]));
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $galleryId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($galleryId, $id)
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            $model = $this->model;

            $data['items'] = $model->joinLanguages(false)->where('id', $id)
                                                         ->getOrFail();

            $view = view('admin.photos.edit', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('photos.index', [$galleryId]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\PhotoRequest  $request
     * @param  int  $galleryId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(PhotoRequest $request, $galleryId, $id)
    {
        $this->model->findOrFail($id)->update($input = $request->all());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        return redirect(cms_route('photos.index', [$galleryId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $galleryId
     * @param  int  $id
     * @return mixed
     */
    public function destroy($galleryId, $id)
    {
        $id = $this->request->get('ids');

        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
