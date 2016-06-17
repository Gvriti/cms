<?php

namespace Models\Traits;

use Models\Language;
use Models\Abstracts\Model;

trait LanguageTrait
{
    /**
     * The Language instance.
     *
     * @var \Models\Language
     */
    protected $languageModel;

    /**
     * Get the language related to this model.
     *
     * @param  \Models\Abstracts\Model  $model
     * @return \Models\Abstracts\Model
     */
    public function language(Model $model)
    {
        return $this->languageModel = new Language($model);
    }

    /**
     * Get the table associated with the Language model.
     *
     * @return string
     */
    public function getLanguageTable()
    {
        return $this->languageTable;
    }

    /**
     * Get the fillable attributes for the Language model.
     *
     * @return array
     */
    public function getLanguageFillable()
    {
        return (array) $this->languageFillable;
    }

    /**
     * Get the not updatable attributes for the Language model.
     *
     * @return array
     */
    public function getLanguageNotUpdatable()
    {
        return (array) $this->languageNotUpdatable;
    }

    /**
     * Get the updatable attributes for the Language model.
     *
     * @param  array   $attributes
     * @param  string  $exclude
     * @return array
     */
    public function getLanguageUpdatable(array $attributes = [], $exclude = null)
    {
        if (is_null($exclude)) {
            $notUpdatable = $this->getLanguageNotUpdatable();
        } else {
            $notUpdatable = (array) $this->{'notUpdatable' . ucfirst($exclude)};
        }

        $updatable = array_flip(array_diff(
            $this->getLanguageFillable(), $notUpdatable
        ));

        return array_intersect_key($attributes, $updatable);
    }

    /**
     * Add a "*_languages" join to the query.
     *
     * @param  bool|string  $language
     * @return \Models\Builder\Builder
     */
    public function joinLanguages($language = true)
    {
        $table = $this->getTable();
        $languageTable = $this->getLanguageTable();

        $columns = ["{$languageTable}.*", "{$languageTable}.id as {$languageTable}_id", "{$table}.*"];

        $query = $this->leftJoin($languageTable, "{$table}.id", '=', "{$languageTable}.{$this->getForeignKey()}")
                      ->addSelect($columns);

        if ($language === true) {
            return $query->currentLanguage();
        } elseif (is_string($language)) {
            return $query->where("{$languageTable}.language", $language);
        }

        return $query;
    }

    /**
     * Add a where `language` clause to the query.
     *
     * @return \Models\Abstracts\Builder
     */
    public function currentLanguage()
    {
        return $this->where("{$this->getLanguageTable()}.language", language());
    }

    /**
     * Update the Eloquent model with its related Language model.
     *
     * @param  array   $attributes
     * @param  array   $options
     * @param  string  $exclude
     * @return int
     */
    public function update(array $attributes = [], array $options = [], $exclude = null)
    {
        parent::update($attributes, $options, $exclude);

        $attributes = $this->getLanguageUpdatable($attributes, $exclude);

        return $this->languageModel->where($this->getForeignKey(), $this->id)
                                   ->where('language', language())
                                   ->update($attributes);
    }

    /**
     * Save a new model with its related Language model and return the instance.
     *
     * @param  array  $attributes
     * @return array
     */
    protected function createLanguage(array $attributes = [])
    {
        $newLanguages = [];

        $languages = languages();

        $attributes[$this->getForeignKey()] = $this->id;

        foreach($languages as $key => $value) {
            $attributes['language'] = $key;

            $newLanguages[] = $this->language($this)->fill($attributes)->save();
        }

        return $newLanguages;
    }
}
