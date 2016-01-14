<?php

namespace Models\Abstracts;

use Models\Page;
use Models\Gallery;

abstract class AbstractHasGallery extends Model
{
    /**
     * Get the Gallery instance.
     *
     * @param  int  $id
     * @return \Models\Gallery|
     *         \Illuminate\Database\Eloquent\Builder
     */
    public function gallery($id = null)
    {
        $model = (new Gallery)->joinLanguages();

        return is_null($id) ? $model : $model->where('id', $id);
    }

    /**
     * Get the data based on admin gallery.
     *
     * @param  \Models\Gallery  $gallery
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminGallery(Gallery $gallery, $columns = ['*'])
    {
        return $this->byGallery($gallery->id)
                    ->orderBy($gallery->admin_order_by, $gallery->admin_sort)
                    ->paginate($gallery->admin_per_page, $columns);
    }

    /**
     * Get the data based on site gallery.
     *
     * @param  \Models\Gallery  $gallery
     * @param  array  $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSiteGallery(Gallery $gallery, $columns = ['*'])
    {
        return $this->byGallery($gallery->id)
                    ->hasFile()
                    ->visible()
                    ->orderBy($gallery->site_order_by, $gallery->site_sort)
                    ->paginate($gallery->site_per_page, $columns);
    }

    /**
     * Build query based on gallery.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function byGallery($id)
    {
        return $this->joinLanguages()->galleryId($id)->currentLanguage();
    }

    /**
     * Get the gallery instance and add a where `type` clause to the query.
     *
     * @param  string|null  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function byType($type = null)
    {
        return $this->gallery()->currentLanguage()->where(
            'type', is_null($type) ? static::TYPE : $type
        );
    }

    /**
     * Add a where `file` is not empty clause to the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function hasFile()
    {
        return $this->whereNotNull('file')->where('file', '!=', '');
    }

    /**
     * Add a where `gallery_id` clause to the query.
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function galleryId($id)
    {
        return $this->where('gallery_id', $id);
    }

    /**
     * Add a where `visible` clause to the query.
     *
     * @param  int  $visible
     * @return \Illuminate\Database\Eloquent\Builder
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
        if (isset($attributes['gallery_id'])) {
            $attributes['position'] = (int) parent::galleryId($attributes['gallery_id'])
                                            ->max('position') + 1;
        } else {
            $attributes['position'] = (int) parent::max('position') + 1;
        }

        return parent::create($attributes);
    }
}
