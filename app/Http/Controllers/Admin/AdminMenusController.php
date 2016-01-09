<?php

namespace App\Http\Controllers\Admin;

use Models\Menu;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;

class AdminMenusController extends Controller
{
    /**
     * The Menu instance.
     *
     * @var \Models\Menu
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Menu  $model
     * @return void
     */
    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.menus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.menus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\MenuRequest  $request
     * @return Response
     */
    public function store(MenuRequest $request)
    {
        $input = $request->all();

        $newModel = $this->model->create($input);

        if ($request->has('close')) {
            return redirect()->route(cms_route('menus.index'))
                                ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('menus.edit', [$newModel->id]))
                ->with('alert', fill_data('success', trans('general.created')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data['item'] = $this->model->findOrFail($id);

        return view('admin.menus.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\MenuRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(MenuRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('menus.index'))
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

    /**
     * Set the specified menu to main.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function setMain(Request $request)
    {
        if ($request->has('id')) {
            $id = (int) $request->get('id');

            $this->model->where('main', 1)->update(['main' => 0]);

            $result = $this->model->findOrFail($id)->update(['main' => 1]);

            if ($result) return response()->json(fill_data($result));
        }

        return response()->json(fill_data(false, trans('general.invalid_input')));
    }
}
