<?php

namespace Models;

use Models\Abstracts\Model;

class _Language extends Model
{
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
