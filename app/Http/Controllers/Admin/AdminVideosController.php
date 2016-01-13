<?php

namespace App\Http\Controllers\Admin;

use Models\Video;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;

class AdminVideosController extends Controller
{
    use VisibilityTrait, PositionableTrait;

    /**
     * The Video instance.
     *
     * @var \Models\Video
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
     * @param  \Models\Video  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Video $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $galleryId
     * @return Response
     */
    public function index($galleryId)
    {
        $model = $this->model;

        $data = $model->gallery()->findOrFail($galleryId);

        $data['items'] = $model->getAdminGallery($data);

        $data['similarTypes'] = $model->byType()->get();

        return view('admin.videos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $galleryId
     * @return Response
     */
    public function create($galleryId)
    {
        if ($this->request->ajax()) {
            $data['item'] = $this->model;
            $data['item']['gallery_id'] = $galleryId;

            $view = view()->make('admin.videos.create', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('videos.index', [$galleryId]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\VideoRequest  $request
     * @param  int  $galleryId
     * @return Response
     */
    public function store(VideoRequest $request, $galleryId)
    {
        $input = $request->all();
        $input['gallery_id'] = $galleryId;

        $newModel = $this->model->create($input);

        if ($request->ajax()) {
            $view = view()->make('admin.videos.item', [
                'model' => $newModel,
                'modelInput' => $input
            ])->render();

            return response()->json(
                fill_data('success', trans('general.created'))
                + ['view' => preg_replace('/\s+/', ' ', trim($view))]
            );
        }

        return redirect(cms_route('videos.index', [$galleryId]));
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
     * @param  int  $galleryId
     * @param  int  $id
     * @return Response
     */
    public function edit($galleryId, $id)
    {
        if ($this->request->ajax()) {
            $model = $this->model;

            $data['items'] = $model->joinLanguages()->where('id', $id)->getOrFail();

            $view = view()->make('admin.videos.edit', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('videos.index', [$galleryId]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\VideoRequest  $request
     * @param  int  $galleryId
     * @param  int  $id
     * @return Response
     */
    public function update(VideoRequest $request, $galleryId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            $input += ['youtube' => getYoutubeEmbed($request->get('file'))];

            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        return redirect(cms_route('videos.index', [$galleryId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $galleryId
     * @param  int  $id
     * @return Response
     */
    public function destroy($galleryId, $id)
    {
        $id = $this->request->get('ids');

        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
