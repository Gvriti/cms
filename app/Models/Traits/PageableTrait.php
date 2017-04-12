<?php

namespace Models\Traits;

use Models\Page;

trait PageableTrait
{
    /**
     * Add a "pages" join to the query.
     *
     * @param  string  $type
     * @param  string  $foreignKey
     * @return \Models\Builder\Builder
     */
    public function joinPage($type = 'right', $foreignKey = 'collection_id')
    {
        return $this->join('pages', $foreignKey, '=', 'type_id', $type)
            ->leftJoin('page_languages', function ($q) {
                return $q->on('page_languages.page_id', '=', 'pages.id')
                    ->where(function ($q) {
                        return $q->where('page_languages.language', '=', language())
                            ->orWhereNull('page_languages.language');
                    });
            })->where('pages.visible', '=', 1)->addSelect([
                'pages.parent_id',
                'pages.slug as parent_slug',
                'page_languages.title as parent_title'
            ]);
    }

    /**
     * Concatenate current model slug to its parent pages slug recursively.
     *
     * @param  int|null  $id
     * @return $this
     */
    public function fullSlug($id = null)
    {
        if (! ($id = (is_null($id) ? $this->parent_id : $id))) {
            return $this;
        }

        if (is_null($model = (new Page)->find($id, ['slug', 'parent_id']))) {
            return $this;
        }

        $model->fullSlug();

        $this->parent_slug = trim($model->slug . '/' . $this->parent_slug, '/');

        return $this;
    }
}
