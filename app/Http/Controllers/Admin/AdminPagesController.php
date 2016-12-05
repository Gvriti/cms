<?php

namespace App\Http\Controllers\Admin;

use Models\Menu;
use Models\Page;
use Models\Collection;
use Illuminate\Http\Request;
use App\Jobs\Admin\AdminDestroy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;

class AdminPagesController extends Controller
{
    use VisibilityTrait, PositionableTrait, MovableTrait;

    /**
     * The Page instance.
     *
     * @var \Models\Page
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
     * @param  \Models\Page  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Page $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $menuId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($menuId)
    {
        $data['menu'] = (new Menu)->findOrFail($menuId);

        $data['items'] = make_tree($this->model->forAdmin($menuId)->get());

        $data['url'] = web_url();

        return view('admin.pages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $menuId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($menuId)
    {
        $data['current'] = $this->model;
        $data['current']->menu_id = $menuId;
        $data['current']->parent_id = (int) $this->request->get('id', 0);

        $data['types'] = cms_pages('types');

        $data['collections'] = (new Collection)->get()->pluck('title', 'id')->toArray();

        return view('admin.pages.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\PageRequest  $request
     * @param  int  $menuId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PageRequest $request, $menuId)
    {
        $input = $request->all();
        $input['menu_id'] = $menuId;

        $model = $this->model->create($input);

        if ($request->has('close')) {
            return redirect(cms_route('pages.index', [$menuId]))
                    ->with('alert', fill_data('success', trans('general.created')));
        }

        return redirect(cms_route('pages.edit', [$menuId, $model->id]))
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
     * @param  int  $menuId
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($menuId, $id)
    {
        $data['items'] = $this->model->joinLanguages(false)
                                    ->where('id', $id)
                                    ->getOrFail();

        $data['types'] = cms_pages('types');

        $data['collections'] = (new Collection)->get()->pluck('title', 'id')->toArray();

        return view('admin.pages.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\PageRequest  $request
     * @param  int  $menuId
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(PageRequest $request, $menuId, $id)
    {
        $this->model->findOrFail($id)->update($input = $request->all());

        if ($request->expectsJson()) {
            if (in_array($type = $request->get('type'), cms_pages('attached'))) {
                $input['typeHtml'] = view(
                    'admin.pages.attached_type', ['input' => $input]
                )->render();
            }

            return response()->json(fill_data(
                'success', trans('general.updated'), $input
            ));
        }

        if ($request->has('close')) {
            return redirect(cms_route('pages.index', [$menuId]))
                    ->with('alert', fill_data('success', trans('general.updated')));
        }

        return redirect()->back()->with('alert', fill_data('success', trans('general.updated')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $menuId
     * @param  int  $id
     * @return mixed
     */
    public function destroy($menuId, $id)
    {
        if ($this->model->hasSubPage($id)) {
            $this->model = null;
        }

        return $this->dispatch(
            new AdminDestroy($this->model, $id)
        );
    }

    /**
     * Get the templates list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplates()
    {
        return response()->json(cms_pages('templates.' . $this->request->get('type')));
    }

    /**
     * Collapse specified page.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function collapse()
    {
        if ($this->request->has('id')) {
            $id = (int) $this->request->get('id', 0);

            $model = $this->model->findOrFail($id);

            if ($model->update(['collapse' => $model->collapse ? 0 : 1])) {
                return response()->json(true);
            }
        }

        return response(trans('general.invalid_input'), 422);
    }
}
