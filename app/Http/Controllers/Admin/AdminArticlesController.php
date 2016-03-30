<?php

namespace App\Http\Controllers\Admin;

use Models\Article;
use Models\Collection;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;

class AdminArticlesController extends Controller
{
    use VisibilityTrait, PositionableTrait, MovableTrait;

    /**
     * The Article instance.
     *
     * @var \Models\Article
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
     * @param  \Models\Article  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Article $model, Request $request)
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

        $data['items'] = $this->model->joinFileId()->getAdminCollection($data['parent']);

        $data['similarTypes'] = $this->model->byType()->get();

        return view('admin.articles.index', $data);
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

        return view('admin.articles.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\ArticleRequest  $request
     * @param  int  $collectionId
     * @return Response
     */
    public function store(ArticleRequest $request, $collectionId)
    {
        $input = $request->all();
        $input['collection_id'] = $collectionId;

        $model = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('articles.index', [$collectionId]))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('articles.edit', [$collectionId, $model->id]))
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
                                     ->getOrFail();

        return view('admin.articles.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\ArticleRequest  $request
     * @param  int  $collectionId
     * @param  int  $id
     * @return Response
     */
    public function update(ArticleRequest $request, $collectionId, $id)
    {
        $input = $request->all();

        $this->model->findOrFail($id)->update($input);

        if ($request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('articles.index', [$collectionId]))
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
