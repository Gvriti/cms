<?php

namespace Models;

use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;
use Models\Abstracts\AbstractHasGallery as Model;

class Video extends Model
{
    use LanguageTrait, PositionableTrait;

    /**
     * Type of the gallery.
     *
     * @var string
     */
    const TYPE = 'videos';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['gallery_id', 'position', 'visible', 'file'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = ['gallery_id'];

    /**
     * Related database table name used by the Language model.
     *
     * @var string
     */
    protected $languageTable = 'video_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = ['video_id', 'language', 'title'];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = ['language'];
}
