<?php

namespace App\Http\Controllers\Admin;

trait VisibilityTrait
{
    /**
     * Update visibility of the specified Eloquent model.
     *
     * @param  int  $id
     * @return Response
     */
    public function visibility($id)
    {
        $request = $this->request;

        $model = $this->model->findOrFail($id);

        if ($model->visible) {
            $model->update(['visible' => 0]);
            $visible = 0;
        } else {
            $model->update(['visible' => 1]);
            $visible = 1;
        }

        if ($request->ajax()) {
            return response()->json(['visible' => $visible]);
        }

        return redirect()->back();
    }
}
