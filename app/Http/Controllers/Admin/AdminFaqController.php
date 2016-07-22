<?php

namespace App\Http\Controllers\Admin;

use Models\Faq;
use Models\Collection;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;

class AdminFaqController extends Controller
{
    use VisibilityTrait, PositionableTrait, MovableTrait;

    /**
     * The Faq instance.
     *
     * @var \Models\Faq
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
     * @param  \Models\Faq  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Faq $model, Request $request)
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
        $data['parent'] = (new Collection)->findOrFail($collectionId);

        $data['items'] = $this->model->getAdminCollection($data['parent']);

        $data['similarTypes'] = $this->model->byType()->get();

        return view('admin.faq.index', $data);
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

        return view('admin.faq.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FaqRequest  $request
     * @param  int  $collectionId
     * @return Response
     */
    public function store(FaqRequest $request, $collectionId)
    {
        $input = $request->all();
        $input['collection_id'] = $collectionId;

        $model = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('faq.index', [$collectionId]))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('faq.edit', [$collectionId, $model->id]))
                ->with('alert', fill_data('success', trans('general.created')));
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
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function edit($collectionId, $id)
    {
        $data['items'] = $this->model->joinLanguages(false)
                                     ->where('id', $id)
                                     ->getOrFail();

        return view('admin.faq.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\FaqRequest  $request
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function update(FaqRequest $request, $collectionId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('faq.index', [$collectionId]))
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
