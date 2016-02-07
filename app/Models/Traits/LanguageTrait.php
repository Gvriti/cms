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
     * @param  \Models\Abstract\Model  $model
     * @return \Models\Abstract\Model
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
        return $this->languageFillable;
    }

    /**
     * Get the not updatable attributes for the Language model.
     *
     * @return array
     */
    public function getLanguageNotUpdatable()
    {
        return $this->languageNotUpdatable;
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
            $notUpdatable = $this->languageNotUpdatable;
        } else {
            $notUpdatable = $this->{'notUpdatable' . ucfirst($exclude)};
        }

        $updatable = array_flip(array_diff(
            (array) $this->languageFillable, (array) $notUpdatable
        ));

        return array_intersect_key($attributes, $updatable);
    }

    /**
     * Add a "_languages" left join to the query.
     *
     * @param  bool|string  $language
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinLanguages($language = false)
    {
        $table = $this->getTable();
        $languageTable = $this->getLanguageTable();
        $foreignKey = str_singular($table) . '_id';

        $columns = ["{$languageTable}.*", "{$languageTable}.id as {$languageTable}_id", "{$table}.*"];

        $query = $this->leftJoin($languageTable, 'id', '=', $foreignKey)
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function currentLanguage()
    {
        return $this->where('language', language());
    }

    /**
     * Update the Eloquent model with its related Language model.
     *
     * @param  array   $attributes
     * @param  string  $exclude
     * @return int
     */
    public function update(array $attributes = [], $exclude = null)
    {
        parent::update($attributes, $exclude);

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

        $currentLanguage = language();

        $title = isset($attributes['title']) ? $attributes['title'] : null;

        $attributes[$this->getForeignKey()] = $this->id;

        foreach($languages as $key => $value) {
            $attributes['language'] = $key;

            if (! is_null($title) && $key != $currentLanguage) {
                $attributes['title'] = $title.' ('.strtoupper($key).')';
            } else {
                $attributes['title'] = $title;
            }

            $newLanguages[] = $this->language($this)->fill($attributes)->save();
        }

        return $newLanguages;
    }
}
