<?php

namespace Models;

use Models\Abstracts\Model;
use Models\Traits\FileableTrait;
use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;

class Page extends Model
{
    use LanguageTrait, PositionableTrait, FileableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'menu_id', 'collection_id', 'type', 'template', 'slug', 'position', 'visible', 'collapse', 'image'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [];

    /**
     * Related database table name used by the Language model.
     *
     * @var string
     */
    protected $languageTable = 'page_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = ['page_id', 'language', 'title', 'short_title', 'description', 'content', 'meta_desc'];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = ['language'];

    /**
     * Get the Menu instance.
     *
     * @param  int  $id
     * @return \Models\Menu|
     *         \Models\Abstracts\Builder
     */
    public function menu($id = null)
    {
        $model = new Menu;

        return is_null($id) ? $model : $model->where('id', $id);
    }

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
     * Add a appropriate query for the cms.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function forAdmin($id = null)
    {
        $query = ! is_null($id) ? $this->menuId($id) : $this;

        return $query->joinLanguages()->joinCollectionType()
                                      ->joinFileId()
                                      ->positionAsc();
    }

    /**
     * Add the appropriate query for the site.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function forSite($id = null)
    {
        $query = ! is_null($id) ? $this->menuId($id) : $this;

        return $query->joinLanguages()->visible();
    }

    /**
     * Get all sub pages.
     *
     * @param  int|null  $id
     * @return \Illuminate\Support\Collection|static[]
     */
    public function getSubPages($id = null)
    {
        $pages = $this->forSite()->parentId($id ?: $this->id)->get();

        $slug = $this->slug;

        return $pages->each(function ($item) use ($slug) {
            $item->original_slug = $item->slug;

            $item->slug = $slug . '/' . $item->slug;
        });
    }

    /**
     * Determine if the model has a sub page.
     *
     * @param  int|null  $id
     * @return bool
     */
    public function hasSubPage($id = null)
    {
        return $this->parentId($id ?: $this->id)->exists();
    }

    /**
     * Get all sibling pages if the model has a parent page.
     *
     * @param  int|null  $id
     * @param  bool      $self
     * @return \Illuminate\Support\Collection|static[]
     */
    public function getSiblingPages($id = null, $self = false)
    {
        if (((int) $id = $id ?: $this->parent_id) == 0) {
            return $this->newCollection();
        }

        $query = $this->forSite();

        if (! $self) {
            $query->where('id', '<>', (int) $this->id);
        }

        $collection = $query->parentId($id)->positionAsc()->get();

        return ($self && $collection->count() > 1) ? $collection
                                                   : $collection->make();
    }

    /**
     * Determine if the model has a parent and sibling page.
     *
     * @param  int|null  $id
     * @return bool
     */
    public function hasSiblingPage($id = null)
    {
        if (((int) $id = $id ?: $this->parent_id) == 0) {
            return false;
        }

        return $this->parentId($id)->where('id', '<>', (int) $this->id)->exists();
    }

    /**
     * Add a query, which is valid for routing.
     *
     * @param  string  $slug
     * @param  int     $parentId
     * @return \Models\Abstracts\Builder
     */
    public function route($slug, $parentId)
    {
        return $this->forSite()->where('slug', $slug)->parentId($parentId);
    }

    /**
     * Add a where `menu_id` clause to the query.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function menuId($id)
    {
        return $this->where('menu_id', $id);
    }

    /**
     * Add a where `parent_id` clause to the query.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function parentId($id)
    {
        return $this->where('parent_id', $id);
    }

    /**
     * Add a where `collection_id` clause to the query.
     *
     * @param  int     $id
     * @param  string  $operator
     * @return \Models\Abstracts\Builder
     */
    public function collectionId($id, $operator = '=')
    {
        return $this->where('collection_id', $operator, $id);
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
     * Concatenate current model slug to its parent pages slug recursively.
     *
     * @param  int|null  $id
     * @return $this
     */
    public function fullSlug($id = null)
    {
        if (! $id = (is_null($id) ? $this->parent_id : $id)) {
            return $this;
        }

        $page = $this->find($id, ['slug', 'parent_id']);

        if (is_null($page)) return $this;

        $this->slug = $page->slug . '/' . $this->slug;

        return $this->fullSlug($page->parent_id);
    }

    /**
     * Add a `collection` left join to the query.
     *
     * @return \Models\Abstracts\Builder
     */
    public function joinCollectionType()
    {
        $table = $this->collection()->getTable();

        $columns = [
            $table . '.title as collection_title',
            $table . '.type as collection_type',
            $this->getTable() . '.*'
        ];

        return $this->leftJoin($table, 'collection_id')->addSelect($columns);
    }

    /**
     * Save a new model and get the instance.
     *
     * @param  array  $attributes
     * @return $this
     */
    public static function create(array $attributes = [])
    {
        if (isset($attributes['menu_id'])) {
            $attributes['position'] = (int) parent::menuId($attributes['menu_id'])
                                            ->max('position') + 1;
        } else {
            $attributes['position'] = (int) parent::max('position') + 1;
        }

        return parent::create($attributes);
    }
}
