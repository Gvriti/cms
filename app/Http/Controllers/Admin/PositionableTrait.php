<?php

namespace App\Http\Controllers\Admin;

use RuntimeException;
use Models\Abstracts\Model;
use Illuminate\Http\Request;

trait PositionableTrait
{
    /**
     * Update the position of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *
     * @throws \RuntimeException
     */
    public function updatePosition()
    {
        if (! isset($this->model) || ! $this->model instanceof Model) {
            throw new RuntimeException('Model not found');
        }

        if (isset($this->request) && $this->request instanceof Request) {
            $request = $this->request;
        } else {
            $request = app(Request::class);
        }

        $data = $request->get('data');

        $params = $request->except('data');

        $nestable = in_array('parent_id', $this->model->getFillable());

        if ($request->expectsJson()) {
            return response()->json(
                $this->model->updatePosition($data, 0, $params, $nestable)
            );
        }

        return redirect()->back();
    }
}
