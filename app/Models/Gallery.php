<?php

namespace Models;

use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;
use Models\Abstracts\HasCollection as Model;

class Gallery extends Model
{
    use LanguageTrait, PositionableTrait;

    /**
     * Type of the collection.
     *
     * @var string
     */
    const TYPE = 'galleries';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'galleries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id', 'type', 'slug', 'admin_order_by', 'admin_sort', 'admin_per_page', 'site_order_by', 'site_sort', 'site_per_page', 'position', 'visible', 'image'
    ];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [
        'type'
    ];

    /**
     * Related database table name used by the Language model.
     *
     * @var string
     */
    protected $languageTable = 'gallery_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = [
        'gallery_id', 'language', 'title', 'meta_desc'
    ];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = [
        'gallery_id', 'language'
    ];
}
