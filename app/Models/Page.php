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
    protected $fillable = ['parent_id', 'menu_id', 'collection_id', 'type', 'slug', 'position', 'visible', 'collapse', 'image'];

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
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->language($this);
    }

    /**
     * Get the Menu instance.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Menu|
     *         \Illuminate\Database\Eloquent\Builder
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
     * @return \Illuminate\Support\Collection|
     *         \Illuminate\Database\Eloquent\Builder
     */
    public function collection($id = null)
    {
        $model = new Collection;

        return is_null($id) ? $model : $model->where('id', $id);
    }

    /**
     * Get all sub pages.
     *
     * @param  mixed  $page
     * @return \Illuminate\Support\Collection|static[]
     */
    public function getSubPages($page)
    {
        if (! $page instanceof self) {
            $page = $this->findOrNew($page);
        }

        $pages = $this->forSite()->parentId((int) $page->id)->get();

        $slug = $page->slug;

        return $pages->isEmpty() ? $pages : $pages->each(function ($item) use ($slug) {
            $item->original_slug = $item->slug;

            $item->slug = $slug . '/' . $item->slug;

            return $item;
        });
    }

    /**
     * Determine if the model has the sub page.
     *
     * @param  int  $id
     * @return bool
     */
    public function hasSubPage($id)
    {
        return $this->parentId($id)->exists();
    }

    /**
     * Add a appropriate query for the cms.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function forAdmin($id = null)
    {
        $query = ! is_null($id) ? $this->menuId($id) : $this;

        return $query->joinLanguages()->joinCollectionType()
                                      ->joinFileId()
                                      ->currentLanguage()
                                      ->positionAsc();
    }

    /**
     * Add the appropriate query for the site.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function forSite($id = null)
    {
        $query = ! is_null($id) ? $this->menuId($id) : $this;

        return $query->joinLanguages()->visible()->currentLanguage();
    }

    /**
     * Add a query, which is valid for routing.
     *
     * @param  string  $slug
     * @param  int     $parentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function route($slug, $parentId)
    {
        return $this->forSite()->where('slug', $slug)->parentId($parentId);
    }

    /**
     * Add a where `menu_id` clause to the query.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function menuId($id)
    {
        return $this->where('menu_id', $id);
    }

    /**
     * Add a where `parent_id` clause to the query.
     *
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function collectionId($id, $operator = '=')
    {
        return $this->where('collection_id', $operator, $id);
    }

    /**
     * Add a where `type` clause to the query.
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function type($type)
    {
        return $this->where('type', $type);
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
     * @return \Illuminate\Database\Eloquent\Builder
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

        $model = parent::create($attributes);

        $model->createLanguage($attributes);

        return $model;
    }
}
