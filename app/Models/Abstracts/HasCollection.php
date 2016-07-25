<?php

namespace Models\Abstracts;

use Models\Collection;
use Models\Traits\PageableTrait;

abstract class HasCollection extends Model
{
    use PageableTrait;

    /**
     * Get the data based on the admin collection.
     *
     * @param  \Models\Collection  $collection
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminCollection(Collection $collection, $columns = ['*'])
    {
        return $this->adminCollection($collection)
                    ->paginate($collection->admin_per_page, $columns);
    }

    /**
     * Get the data based on the public collection.
     *
     * @param  \Models\Collection  $collection
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPublicCollection(Collection $collection, $columns = ['*'])
    {
        return $this->publicCollection($collection)
                    ->paginate($collection->site_per_page, $columns);
    }

    /**
     * Build a query based on the admin collection.
     *
     * @param  \Models\Collection  $collection
     * @return \Models\Builder\Builder
     */
    public function adminCollection(Collection $collection)
    {
        return $this->forAdmin($collection->id)
                    ->orderBy($collection->admin_order_by, $collection->admin_sort);
    }

    /**
     * Build a query based on the public collection.
     *
     * @param  \Models\Collection  $collection
     * @return \Models\Builder\Builder
     */
    public function publicCollection(Collection $collection)
    {
        return $this->forPublic($collection->id)
                    ->orderBy($collection->site_order_by, $collection->site_sort);
    }

    /**
     * Add the appropriate query for the admin.
     *
     * @param  int|null  $id
     * @param  mixed  $language
     * @return \Models\Builder\Builder
     */
    public function forAdmin($id = null, $language = true)
    {
        $query = ! is_null($id) ? $this->collectionId($id) : $this;

        return $query->joinLanguages($language);
    }

    /**
     * Add the appropriate query for the public.
     *
     * @param  int|null  $id
     * @param  mixed  $language
     * @return \Models\Builder\Builder
     */
    public function forPublic($id = null, $language = true)
    {
        return $this->forAdmin($id, $language)->visible();
    }

    /**
     * Build a query based on the slug.
     *
     * @param  string    $slug
     * @param  int|null  $id
     * @return \Models\Builder\Builder
     */
    public function bySlug($slug, $id = null)
    {
        return $this->where('slug', $slug)->forPublic($id);
    }

    /**
     * Get the collection instance and add a where "type" clause to the query.
     *
     * @param  string|null  $type
     * @return \Models\Builder\Builder
     */
    public function byType($type = null)
    {
        return (new Collection)->where(
            'type', is_null($type) ? static::TYPE : $type
        );
    }

    /**
     * Add a where "collection_id" clause to the query.
     *
     * @param  int  $id
     * @return \Models\Builder\Builder
     */
    public function collectionId($id)
    {
        return $this->where('collection_id', $id);
    }

    /**
     * Add a where "visible" clause to the query.
     *
     * @param  int  $visible
     * @return \Models\Builder\Builder
     */
    public function visible($visible = 1)
    {
        return $this->where('visible', (int) $visible);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(array $attributes = [])
    {
        if (isset($attributes['collection_id'])) {
            $attributes['position'] = (int) parent::collectionId($attributes['collection_id'])
                                            ->max('position') + 1;
        } else {
            $attributes['position'] = (int) parent::max('position') + 1;
        }

        return parent::create($attributes);
    }
}
