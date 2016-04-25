<?php

namespace Models;

use Closure;
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
    protected $fillable = [
        'parent_id', 'menu_id', 'type_id', 'type', 'template', 'slug', 'position', 'visible', 'collapse', 'image'
    ];

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
    protected $languageFillable = [
        'page_id', 'language', 'title', 'short_title', 'description', 'content', 'meta_desc'
    ];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = [
        'page_id', 'language'
    ];

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
     * Add the appropriate query for the cms.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function forAdmin($id = null)
    {
        $query = ! is_null($id) ? $this->menuId($id) : $this;

        return $query->joinLanguages()
                    ->joinCollectionType()
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
     * Get the base page.
     *
     * @param  int|null  $id
     * @param  \Closure|null  $id
     * @return static
     */
    public function getBasePage($id = null, Closure $callback = null)
    {
        if (! ($id = ($id ?: $this->parent_id))) {
            return $this;
        }

        if (is_null($page = $this->where('id', $id)->forSite()->first())) {
            return $this;
        }

        if (! $page->parent_id || (! is_null($callback) && $callback($page))) {
            return $page;
        }

        return $this->getBasePage($page->parent_id);
    }

    /**
     * Get sub pages.
     *
     * @param  bool|int  $recursive
     * @param  int|null  $id
     * @return \Illuminate\Support\Collection
     */
    public function getSubPages($recursive = false, $id = null)
    {
        $pages = $this->forSite()->parentId($id ?: $this->id)->positionAsc()->get();

        if (is_int($recursive) && $recursive > 0) {
            $recursive -= 1;
        }

        return $recursive ? $pages->each(function ($item) use ($recursive) {
            $item->subPages = $this->getSubPages($recursive, $item->id);
        }) : $pages;
    }

    /**
     * Determine if the model has a sub page.
     *
     * @return bool
     */
    public function hasSubPage()
    {
        return $this->parentId($this->id)->exists();
    }

    /**
     * Get sibling pages if the model has a parent page.
     *
     * @param  bool|int  $recursive
     * @param  bool  $self
     * @param  bool  $firstLevel
     * @return \Illuminate\Support\Collection
     */
    public function getSiblingPages($recursive = false, $self = true, $firstLevel = false)
    {
        if (! $firstLevel && ! $this->parent_id) {
            return $this->newCollection();
        }

        $pages = $this->forSite();

        if (! $self) {
            $pages->where('id', '<>', (int) $this->id);
        }

        $pages = $pages->parentId($this->parent_id)
                        ->menuId($this->menu_id)
                        ->positionAsc()
                        ->get();

        if ($self && $pages->count() > 1) {
            return $recursive ? $pages->each(function ($item) use ($recursive) {
                $item->subPages = $this->getSubPages($recursive, $item->id);
            }) : $pages;
        } else {
            return $pages->make();
        }
    }

    /**
     * Determine if the model has a parent page.
     *
     * @return bool
     */
    public function hasSiblingPage()
    {
        if (! $this->parent_id) {
            return false;
        }

        return $this->parentId($this->parent_id)->exists();
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
     * Add a where `type_id` clause to the query.
     *
     * @param  int     $id
     * @param  string  $operator
     * @return \Models\Abstracts\Builder
     */
    public function typeId($id, $operator = '=')
    {
        return $this->where('type_id', $operator, $id);
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
        if (! ($id = (is_null($id) ? $this->parent_id : $id))) {
            return $this;
        }

        if (is_null($page = (new static)->find($id, ['slug', 'parent_id']))) {
            return $this;
        }

        $this->slug = trim($page->slug . '/' . $this->slug, '/');

        return $this->fullSlug($page->parent_id);
    }

    /**
     * Add a `collection` join to the query.
     *
     * @return \Models\Abstracts\Builder
     */
    public function joinCollectionType()
    {
        $table = (new Collection)->getTable();

        $columns = [
            $table . '.title as collection_title',
            $table . '.type as collection_type',
            $this->getTable() . '.*'
        ];

        return $this->leftJoin($table, 'type_id', '=', $table . '.id')
                    ->addSelect($columns);
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
