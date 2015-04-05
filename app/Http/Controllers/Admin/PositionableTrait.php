<?php

namespace App\Http\Controllers\Admin;

trait PositionableTrait
{
    /**
     * Update the position of the resource.
     *
     * @return Response|bool
     */
    public function updatePosition()
    {
        if ($this->request->ajax()) {
            $data = $this->request->get('data');

            $params = $this->request->except('data');

            $nestable = in_array('parent_id', $this->model->getFillable());

            $result = $this->model->updatePosition($data, 0, $params, $nestable);

            return response()->json($result);
        }

        return false;
    }
}
