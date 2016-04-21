<?php

namespace Models\Traits;

use Models\Page;

trait PageableTrait
{
    /**
     * Add a "pages" join to the query.
     *
     * @param  array|mixed  $column
     * @param  string  $foreignKey
     * @return \Digital\Repositories\Eloquent\EloquentBuilder
     */
    public function joinPage($column = null, $foreignKey = 'collection_id')
    {
        $column = $column ?: [
            'pages.parent_id',
            'pages.slug as parent_slug',
            'page_languages.title as parent_title'
        ];

        return $this->leftJoin('pages', $foreignKey, '=', 'type_id')
                    ->join('page_languages', function ($query) {
                        $query->on('pages.id', '=', 'page_id')
                              ->where('page_languages.language', '=', language());
                    })->addSelect($column);
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

        if (is_null($page = (new Page)->find($id, ['slug', 'parent_id']))) {
            return $this;
        }

        $page->fullSlug();

        $this->parent_slug = trim($page->slug . '/' . $this->parent_slug, '/');

        return $this;
    }
}
