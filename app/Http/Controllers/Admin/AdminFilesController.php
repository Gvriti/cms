<?php

namespace App\Http\Controllers\Admin;

use Models\File;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FileRequest;

class AdminFilesController extends Controller
{
    use PositionableTrait, VisibilityTrait;

    /**
     * The File instance.
     *
     * @var \Models\File
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
     * @param  \Models\File  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(File $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string $modelName
     * @param  int    $modelId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($modelName, $modelId)
    {
        $data['parent'] = $this->model->makeForeign();

        $data['items'] = $this->model->getByRoute();

        $data['modelName'] = $modelName;

        $data['routeName'] = config("cms.files.{$modelName}.route_name");

        return view('admin.files.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string $modelName
     * @param  int    $modelId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create($modelName, $modelId)
    {
        if ($this->request->expectsJson()) {
            $data['current'] = $this->model;

            $view = view('admin.files.create', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('files.index', [$modelName, $modelId]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FileRequest  $request
     * @param  string  $modelName
     * @param  int     $modelId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(FileRequest $request, $modelName, $modelId)
    {
        $model = $this->model->create($input = $request->all());

        if ($request->expectsJson()) {
            $view = view('admin.files.item', [
                'item' => $model,
                'itemInput' => $input
            ])->render();

            return response()->json(
                fill_data('success', trans('general.created'))
                + ['view' => preg_replace('/\s+/', ' ', trim($view))]
            );
        }

        return redirect(cms_route('files.index', [$modelName, $modelId]));
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function show()
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $modelName
     * @param  int     $modelId
     * @param  int     $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($modelName, $modelId, $id)
    {
        if ($this->request->expectsJson()) {
            $data['items'] = $this->model->joinLanguages(false)
                                         ->where('id', $id)
                                         ->getOrFail();

            $view = view('admin.files.edit', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('files.index', [$modelName, $modelId]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FileRequest  $request
     * @param  string  $modelName
     * @param  int     $modelId
     * @param  int     $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(FileRequest $request, $modelName, $modelId, $id)
    {
        $this->model->findOrFail($id)->update($input = $request->all());

        if ($request->expectsJson()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        return redirect(cms_route('files.index', [$modelName, $modelId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $modelName
     * @param  int     $modelId
     * @param  int     $id
     * @return mixed
     */
    public function destroy($modelName, $modelId, $id)
    {
        $id = $this->request->get('ids');

        if (count($id) == 1) {
            $id = $id[0];
        }

        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
