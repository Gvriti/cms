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
     * @return \Models\Abstracts\Builder
     */
    public function joinPage($type = 'left', $foreignKey = 'collection_id')
    {
        return $this->join('pages', $foreignKey, '=', 'type_id', $type)
                    ->leftjoin('page_languages', function ($q) {
                        $q->on('page_languages.page_id', '=', 'pages.id')
                            ->where(function ($q) {
                                return $q->where('page_languages.language', '=', language())
                                        ->orWhereNull('page_languages.language');
                            });
                    })->addSelect([
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

        if (is_null($page = (new Page)->find($id, ['slug', 'parent_id']))) {
            return $this;
        }

        $page->fullSlug();

        $this->parent_slug = trim($page->slug . '/' . $this->parent_slug, '/');

        return $this;
    }
}
