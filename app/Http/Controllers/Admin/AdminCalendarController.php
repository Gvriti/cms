<?php

namespace App\Http\Controllers\Admin;

use Models\Calendar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminCalendarController extends Controller
{
    /**
     * The Calendar instance.
     *
     * @var \Models\Calendar
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
     * @param  \Models\Calendar  $model
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Calendar $model, Request $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    /**
     * Display a listing of the events.
     *
     * @return Response
     */
    public function index()
    {
        $data['items'] = $this->model->getInactive();

        return view('admin.calendar.index', $data);
    }

    /**
     * Get all of the events.
     *
     * @return Response
     */
    public function events()
    {
        $date = $this->request->only(['start', 'end']);

        $data = $this->model->getActive($date['start'], $date['end']);

        return response()->json($data);
    }

    /**
     * Create or update a event.
     *
     * @return Response
     */
    public function save()
    {
        $this->validate($this->request, [
            'title' => 'required|min:3'
        ]);

        if ($this->request->has('id')) {
            $id = $this->request->get('id');

            $result = $this->model->findOrFail($id)->updateEvent($this->request);
        } else {
            $input = $this->request->all();
            $input['color'] = $this->model->getRandomColor();

            $result = $this->model->create($input);
        }

        if ($this->request->ajax()) {
            return response()->json(fill_data(
                'success', trans('general.saved'), $result->getAttributes()
            ));
        }

        return redirect()->back();
    }

    /**
     * Remove the specified event.
     *
     * @return Response
     */
    public function destroy()
    {
        $id = $this->request->get('id');

        $result = $this->model->delete($id);

        if ($this->request->ajax()) {
            return response()->json($result);
        }

        return redirect()->back();
    }
}
