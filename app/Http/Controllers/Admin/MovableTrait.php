<?php

namespace App\Http\Controllers\Admin;

use Models\Abstracts\Model;

trait MovableTrait
{
    /**
     * Move item to the specified direction.
     *
     * @param  int  $id
     * @return Response
     */
    public function move($id)
    {
        $input = $this->request->only(['id', 'column', 'column_value', 'recursive']);

        if ($id != $input['column_value']) {
            app('db')->transaction(function () use ($input) {
                $model = $this->model->findOrFail($input['id']);

                $position = $this->model->where($input['column'], $input['column_value'])->max('position');

                $attributes = [$input['column'] => $input['column_value'], 'position' => $position + 1];

                if ($recursive = ! empty($input['recursive'])) {
                    $attributes['parent_id'] = 0;
                }

                $model->update($attributes);

                if ($recursive) {
                    $this->updateMenu($model, $input['column'], $input['column_value']);
                }
            });
        }

        if ($this->request->ajax()) {
            return msg_render('success', 'general.updated', $input);
        }

        return redirect()->back()->with('alert', msg_result('success', 'general.updated'));
    }

    /**
     * Move item recursively with its child item(s).
     *
     * @param  \Models\Abstracts\Model  $model
     * @param  string  $column
     * @param  int     $columnValue
     * @return void
     */
    protected function updateMenu(Model $model, $column, $columnValue)
    {
        $items = $this->model->where('parent_id', $model->id)->get();

        if (! $items->isEmpty()) {
            foreach ($items as $item) {
                $item->update([$column => $columnValue]);

                $this->updateMenu($item, $column, $columnValue);
            }
        }
    }
}
