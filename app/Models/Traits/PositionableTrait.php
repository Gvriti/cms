<?php

namespace Models\Traits;

use Models\Language;
use Models\Abstracts\Model;

trait PositionableTrait
{
    /**
     * Update the position of the Eloquent models.
     *
     * @param  array  $data
     * @param  int    $parentId
     * @param  array  $params
     * @param  bool   $nestable
     * @return bool
     */
    public function updatePosition(array $data, $parentId = 0, array $params = [], $nestable = false)
    {
        if (! $nestable) {
            if (! is_array($data = $this->movePosition($data, $params))) {
                return $data;
            }
        }

        app('db')->transaction(function () use ($data, $parentId, $params, $nestable) {
            $attributes = [];

            $position = 0;

            foreach($data as $key => $item) {
                if ($nestable) {
                    $position++;
                    $attributes['parent_id'] = $parentId;
                } else {
                    $position = $item['pos'];
                }

                $attributes['position'] = $position;

                $this->where('id', $item['id'])->update($attributes);

                if (isset($item['children'])) {
                    $this->updatePosition($item['children'], $item['id'], $params, $nestable);
                }
            }
        });

        return true;
    }

    /**
     * Update the position of the Eloquent models by specified order and direction.
     *
     * @param  array  $data
     * @param  array  $params
     * @return array|bool
     */
    private function movePosition(array $data, array $params = [])
    {
        if ($dragging = array_diff(['move', 'orderBy'], array_keys($params))) {
            return $data;
        }

        if ($params['move'] == 'next') {
            if ($params['orderBy'] == 'desc') {
                $posFunc = function (&$value) {
                    return $value['pos'] += 1;
                };
            } else {
                $posFunc = function (&$value) {
                    return $value['pos'] -= 1;
                };
            }

            $newPos = end($data)['pos'] - 1;
        } else {
            if ($params['orderBy'] == 'asc') {
                $posFunc = function (&$value) {
                    return $value['pos'] += 1;
                };
            } else {
                $posFunc = function (&$value) {
                    return $value['pos'] -= 1;
                };
            }

            $newPos = end($data)['pos'] + 1;
        }

        array_walk($data, $posFunc);

        $newData = $this->where(['position' => $newPos])->first(['id']);

        if (! $newData) return false;

        $dataCount = count($data);

        $data[0]['pos'] = $newPos;
        $data[$dataCount]['pos'] = $params['move'] == 'next' ? $newPos + 1 : $newPos - 1;
        $data[$dataCount]['id'] = $newData['id'];

        return $data;
    }

    /**
     * Add an "order by" position asc clause to the query.
     *
     * @return \Models\Abstracts\Builder
     */
    public function positionAsc()
    {
        return $this->orderBy('position', 'asc');
    }

    /**
     * Add an "order by" position desc clause to the query.
     *
     * @return \Models\Abstracts\Builder
     */
    public function positionDesc()
    {
        return $this->orderBy('position', 'desc');
    }

    /**
     * Save a new model and get the instance.
     *
     * @param  array  $attributes
     * @return $this
     */
    public static function create(array $attributes = [])
    {
        $attributes['position'] = (int) parent::max('position') + 1;

        return parent::create($attributes);
    }
}
