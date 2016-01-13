<?php

namespace Models;

use Models\Abstracts\Model;
use Models\Traits\LanguageTrait;

class Localization extends Model
{
    use LanguageTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'localization';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = ['name'];

    /**
     * Related database table name used by the Language model.
     *
     * @var string
     */
    protected $languageTable = 'localization_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = ['localization_id', 'language', 'value'];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = ['language'];
}
