<?php

namespace Models\Abstracts;

use Models\Builder\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Http\Exception\HttpResponseException;

abstract class Model extends BaseModel
{
    /**
     * The Eloquent builder instance.
     *
     * @var \Models\Builder\Builder|null
     */
    protected $builder;

    /**
     * Indicates if the model has a languages.
     *
     * @var bool
     */
    protected $hasLanguages = false;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set language model if it's used into the called model.
        if (method_exists(get_called_class(), 'setLanguage')) {
            $this->setLanguage($this);

            $this->hasLanguages = true;
        }
    }

    /**
     * Determine if a model has a languages.
     *
     * @return bool
     */
    public function hasLanguages()
    {
        return $this->hasLanguages;
    }

    /**
     * Get the updatable attributes for the model.
     *
     * @param  array  $attributes
     * @param  string|null  $exclude
     * @return array
     */
    public function getUpdatable(array $attributes = [], $exclude = null)
    {
        if (! ($hasUpdatable = ! empty($this->updatable)) && empty($this->notUpdatable)) {
            return $attributes;
        }

        $property = is_null($exclude) ? 'updatable' : 'updatable' . ucfirst($exclude);

        if ($hasUpdatable) {
            $fillable = array_flip(array_intersect($this->fillable, (array) $this->{$property}));
        } else {
            $fillable = array_flip(array_diff(
                $this->fillable,
                (array) $this->{'not' . ucfirst($property)}
            ));
        }

        return array_intersect_key($attributes, $fillable);
    }

    /**
     * {@inheritdoc}
     */
    public function newEloquentBuilder($builder)
    {
        if ($this->builder instanceof Builder) {
            $builder = $this->builder;

            $this->builder = null;

            return $builder;
        }

        return new Builder($builder, $this);
    }

    /**
     * Set the Eloquent builder instance.
     *
     * @param  \Models\Builder\Builder  $builder
     * @return $this
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Find a model by its query or return new static.
     *
     * @param  array  $columns
     * @return \Illuminate\Support\Collection|static
     */
    public function firstNew($columns = ['*'])
    {
        if (! is_null($model = $this->first($columns))) {
            return $model;
        }

        return new static;
    }

    /**
     * Execute the query and get the specified attribute.
     *
     * @param  string  $attribute
     * @param  mixed  $default
     * @return mixed
     */
    public function getAttr($attribute, $default = null)
    {
        if (! is_null($model = $this->first([$attribute]))) {
            return $model->{$attribute} ?: $default;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(array $attributes = [])
    {
        $model = parent::create($attributes);

        // Create language model if it's exists in this model.
        if (method_exists(get_called_class(), 'createLanguage')) {
            $model->createLanguage($attributes);
        }

        return $model;
    }

    /**
     * Update the model in the database.
     *
     * @param  array   $attributes
     * @param  array   $options
     * @param  string  $exclude
     * @return bool|int
     */
    public function update(array $attributes = [], array $options = [], $exclude = null)
    {
        $attributes = $this->getUpdatable($attributes, $exclude);

        return parent::update($attributes, $options);
    }

    /**
     * Delete the model from the database.
     *
     * @param  int|null  $id
     * @return bool|null
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public function delete($id = null)
    {
        if (is_null($id)) {
            return parent::delete();
        }

        if (! is_null($model = $this->find($id))) {
            return $model->delete();
        }
    }

    /**
     * Throw new HttpResponseException.
     *
     * @param  \Illuminate\Database\QueryException  $e
     * @return void
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    protected function queryExceptionResponse(QueryException $e)
    {
        $parameters = explode('\'', $e->previous->getMessage());

        $parameters = isset($parameters[1]) ? ['name' => $parameters[1]] : [];

        if (request()->expectsJson()) {
            $response = response()->json(fill_db_data($e->errorInfo[1], $parameters));
        } else {
            $response = redirect()->back()
                                  ->with('alert', fill_db_data($e->errorInfo[1], $parameters))
                                  ->withInput();
        }

        throw new HttpResponseException($response);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($key)
    {
        return $this->getAttributeValue($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getAttributeValue($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }
}
