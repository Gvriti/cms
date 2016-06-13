<?php

namespace App\Http\Controllers\Admin;

use Models\File;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FileRequest;

class AdminFilesController extends Controller
{
    use VisibilityTrait, PositionableTrait;

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
     * @param  string $routeName
     * @param  int    $routeId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($routeName, $routeId)
    {
        $data['parent'] = $this->model->makeForeign();

        $data['items'] = $this->model->getByRoute();

        return view('admin.files.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string $routeName
     * @param  int    $routeId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create($routeName, $routeId)
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            $data['current'] = $this->model;

            $view = view('admin.files.create', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('files.index', [$routeName, $routeId]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FileRequest  $request
     * @param  string  $routeName
     * @param  int     $routeId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(FileRequest $request, $routeName, $routeId)
    {
        $input = $request->all();

        $input['route_name'] = $routeName;
        $input['route_id'] = $routeId;

        $model = $this->model->create($input);

        if ($request->ajax() || $request->wantsJson()) {
            $view = view('admin.files.item', [
                'model' => $model,
                'modelInput' => $input
            ])->render();

            return response()->json(
                fill_data('success', trans('general.created'))
                + ['view' => preg_replace('/\s+/', ' ', trim($view))]
            );
        }

        return redirect(cms_route('files.index', [$routeName, $routeId]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $routeName
     * @param  int     $routeId
     * @param  int     $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($routeName, $routeId, $id)
    {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            $data['items'] = $this->model->joinLanguages(false)
                                         ->where('id', $id)
                                         ->getOrFail();

            $view = view('admin.files.edit', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('files.index', [$routeName, $routeId]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FileRequest  $request
     * @param  string  $routeName
     * @param  int     $routeId
     * @param  int     $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(FileRequest $request, $routeName, $routeId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        return redirect(cms_route('files.index', [$routeName, $routeId]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $routeName
     * @param  int     $routeId
     * @param  int     $id
     * @return mixed
     */
    public function destroy($routeName, $routeId, $id)
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
