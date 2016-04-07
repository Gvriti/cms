<?php

namespace Models\Abstracts;

use Models\Page;
use Models\Collection;
use Models\Traits\PageableTrait;

abstract class AbstractHasCollection extends Model
{
    use PageableTrait;

    /**
     * Get the Collection instance.
     *
     * @param  int  $id
     * @return \Models\Collection|
     *         \Models\Abstracts\Builder
     */
    public function collection($id = null)
    {
        $model = new Collection;

        return is_null($id) ? $model : $model->where('id', $id);
    }

    /**
     * Get the data based on admin collection.
     *
     * @param  \Models\Collection  $collection
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminCollection(Collection $collection, $columns = ['*'])
    {
        return $this->collectionId($collection->id)
                    ->forAdmin()
                    ->orderBy($collection->admin_order_by, $collection->admin_sort)
                    ->paginate($collection->admin_per_page, $columns);
    }

    /**
     * Get the data based on site collection.
     *
     * @param  \Models\Collection  $collection
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSiteCollection(Collection $collection, $columns = ['*'])
    {
        return $this->collectionId($collection->id)
                    ->forSite()
                    ->orderBy($collection->site_order_by, $collection->site_sort)
                    ->paginate($collection->site_per_page, $columns);
    }

    /**
     * Add the appropriate query for the cms.
     *
     * @param  int|null  $id
     * @return \Models\Abstracts\Builder
     */
    public function forAdmin($id = null)
    {
        $query = ! is_null($id) ? $this->collectionId($id) : $this;

        return $query->joinLanguages();
    }

    /**
     * Add the appropriate query for the site.
     *
     * @param  int|null  $id
     * @return \Models\Abstracts\Builder
     */
    public function forSite($id = null)
    {
        return $this->forAdmin($id)->visible();
    }

    /**
     * Build a query based on the slug.
     *
     * @param  string    $slug
     * @param  int|null  $id
     * @return \Models\Abstracts\Builder
     */
    public function bySlug($slug, $id = null)
    {
        return $this->where('slug', $slug)->forSite($id);
    }

    /**
     * Get the collection instance and add a where `type` clause to the query.
     *
     * @param  string|null  $type
     * @return \Models\Abstracts\Builder
     */
    public function byType($type = null)
    {
        return (new Collection)->where(
            'type', is_null($type) ? static::TYPE : $type
        );
    }

    /**
     * Add a where `collection_id` clause to the query.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function collectionId($id)
    {
        return $this->where('collection_id', $id);
    }

    /**
     * Add a where `visible` clause to the query.
     *
     * @param  int  $visible
     * @return \Models\Abstracts\Builder
     */
    public function visible($visible = 1)
    {
        return $this->where('visible', (int) $visible);
    }

    /**
     * Save a new model and get the instance.
     *
     * @param  array  $attributes
     * @return $this
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
