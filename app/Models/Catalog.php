<?php

namespace Models;

use Models\Traits\FileableTrait;
use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;
use Models\Abstracts\AbstractHasCollection;

class Catalog extends AbstractHasCollection
{
    use LanguageTrait, PositionableTrait, FileableTrait;

    /**
     * Type of the collection.
     *
     * @var string
     */
    const TYPE = 'catalog';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'catalog';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['collection_id', 'slug', 'position', 'visible', 'price', 'image'];

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
    protected $languageTable = 'catalog_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = ['catalog_id', 'language', 'title', 'short_title', 'description', 'content', 'meta_desc'];

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
}
