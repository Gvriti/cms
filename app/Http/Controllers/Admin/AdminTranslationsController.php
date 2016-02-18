<?php

namespace App\Http\Controllers\Admin;

use Models\Translation;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TranslationRequest;

class AdminTranslationsController extends Controller
{
    /**
     * The Translation instance.
     *
     * @var \Models\Translation
     */
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param  \Models\Translation  $model
     * @return void
     */
    public function __construct(Translation $model)
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
        $data['items'] = $this->model->joinLanguages()->get();

        return view('admin.translations.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $data['current'] = $this->model;

        return view('admin.translations.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\TranslationRequest  $request
     * @return Response
     */
    public function store(TranslationRequest $request)
    {
        $newModel = $this->model->create($request->all());

        if ($request->has('close')) {
            return redirect()->route(cms_route('translations.index'))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('translations.edit', [$newModel->id]))
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
        $data['items'] = $this->model->joinLanguages(false)
                                     ->where('id', $id)
                                     ->getOrFail();

        return view('admin.translations.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\TranslationRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(TranslationRequest $request, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('translations.index'))
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
     * Get the translation modal form by speicific name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function getModal(Request $request)
    {
        if (! ($name = $request->get('name'))) {
            return response('Invalid name.', 422);
        }

        $data['items'] = $this->model->where('name', $name)
                                     ->joinLanguages(false)
                                     ->get();

        if ($data['items']->isEmpty()) {
            $data['current'] = $this->model;
            $data['current']->name = $name;

            $form = 'create';
        } else {
            $form = 'edit';
        }

        return view('admin.translations.modal.' . $form, $data);
    }

    /**
     * Create/Update a translation model.
     *
     * @param  \App\Http\Requests\Admin\TranslationRequest  $request
     * @return Response
     */
    public function postModal(TranslationRequest $request)
    {
        if ($id = $request->get('id')) {
            $this->model->findOrFail($id)->update($request->all());
        } else {
            $this->model->create($request->all());
        }

        return response()->json([
            'name' => $request->get('name'),
            'value' => $request->get('value')
        ]);
    }
}
