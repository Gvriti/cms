<?php

namespace Models;

use Models\Abstracts\Model;

class Language extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [];

    /**
     * Create a new Language model instance.
     *
     * @param  \Models\Abstracts\Model  $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->table = $model->getLanguageTable();

        $this->fillable = $model->getLanguageFillable();

        $this->notUpdatable = $model->getLanguageNotUpdatable();
    }
}
