<?php

namespace App\Http\Controllers\Admin;

use Models\Abstracts\Model;
use Illuminate\Http\Request;

trait VisibilityTrait
{
    /**
     * Update visibility of the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function visibility(Request $request, $id)
    {
        if (! $this->model instanceof Model) {
            return response('Model not found.', 500);
        }

        $model = $this->model->findOrFail($id);

        if ($model->visible) {
            $model->update(['visible' => $visible = 0]);
        } else {
            $model->update(['visible' => $visible = 1]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['visible' => $visible]);
        }

        return redirect()->back();
    }
}
