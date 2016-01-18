<?php

namespace App\Http\Controllers\Admin;

use Models\Slider;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderRequest;

class AdminSliderController extends Controller
{
    use VisibilityTrait, PositionableTrait;

    /**
     * The Slider instance.
     *
     * @var \Models\Slider
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
     * @param  \Models\Slider  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Slider $model, Request $request)
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
        $data['items'] = $this->model->forAdmin()->get();

        return view('admin.slider.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if ($this->request->ajax()) {
            $data['current'] = $this->model;

            $view = view()->make('admin.slider.create', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('slider.index'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\SliderRequest  $request
     * @return Response
     */
    public function store(SliderRequest $request)
    {
        $input = $request->all();

        $newModel = $this->model->create($input);

        if ($request->ajax()) {
            $view = view()->make('admin.slider.item', [
                'model' => $newModel,
                'modelInput' => $input
            ])->render();

            return response()->json(
                fill_data('success', trans('general.created'))
                + ['view' => preg_replace('/\s+/', ' ', trim($view))]
            );
        }

        return redirect(cms_route('slider.index'));
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
        if ($this->request->ajax()) {
            $data['items'] = $this->model->joinLanguages()->where('id', $id)
                                                          ->getOrFail();

            $view = view()->make('admin.slider.edit', $data)->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('slider.index'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\SliderRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SliderRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        return redirect(cms_route('slider.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $id = $this->request->get('ids');

        return $this->dispatch(
            new AdminDestroy($this->model, $id, false)
        );
    }
}
