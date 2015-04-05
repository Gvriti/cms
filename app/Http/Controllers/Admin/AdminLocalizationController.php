<?php

namespace App\Http\Controllers\Admin;

use Models\Localization;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocalizationRequest;

class AdminLocalizationController extends Controller
{
    /**
     * The Localization instance.
     *
     * @var \Models\Localization
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Localization  $model
     * @return void
     */
    public function __construct(Localization $model)
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
        $data['items'] = $this->model->joinLanguages()->currentLanguage()->get();

        return view('admin.localization.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.localization.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\LocalizationRequest  $request
     * @return Response
     */
    public function store(LocalizationRequest $request)
    {
        $newModel = $this->model->create($request->all());

        if ($request->has('close')) {
            return redirect()->route(cms_route('localization.index'))
                                ->with('alert', msg_result('success', 'general.created'));
        }

        return redirect(cms_route('localization.edit', [$newModel->id]))
                ->with('alert', msg_result('success', 'general.created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $model = $this->model;

        $data['items'] = $model->joinLanguages()->where('id', $id)->getOrFail();

        return view('admin.localization.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\LocalizationRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(LocalizationRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return msg_render('success', 'general.updated', $input);
        }

        if ($request->has('close')) {
            return redirect(cms_route('localization.index'))
                    ->with('alert', msg_result('success', 'general.updated'));
        }

        return redirect()->back()->with('alert', msg_result('success', 'general.updated'));
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
