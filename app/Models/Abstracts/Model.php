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
    private $builder;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set language model if it's used in this model.
        if (method_exists(get_called_class(), 'language')) {
            $this->language($this);
        }
    }

    /**
     * Get the updatable attributes for the model.
     *
     * @param  array   $attributes
     * @param  string  $exclude
     * @return array
     */
    public function getUpdatable(array $attributes = [], $exclude = null)
    {
        if (is_null($exclude)) {
            $notUpdatable = $this->notUpdatable;
        } else {
            $notUpdatable = $this->{'notUpdatable' . ucfirst($exclude)};
        }

        $updatable = array_flip(array_diff($this->fillable, (array) $notUpdatable));

        return array_intersect_key($attributes, $updatable);
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
     * @param  string  $exclude
     * @return bool|int
     */
    public function update(array $attributes = [], $exclude = null)
    {
        $attributes = $this->getUpdatable($attributes, $exclude);

        return $this->fill($attributes)->save();
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public function save(array $options = [])
    {
        try {
            parent::save($options);
        } catch (QueryException $e) {
            $this->queryExceptionResponse($e);
        }

        return $this;
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
        try {
            if (! is_null($id)) {
                $this->findOrFail($id)->delete();
            }

            return parent::delete();
        } catch (QueryException $e) {
            $this->queryExceptionResponse($e);
        }
    }

    /**
     * Destroy the models for the given IDs.
     *
     * @param  array|int  $ids
     * @return int
     *
     * @throws \Illuminate\Http\Exception\HttpResponseException
     */
    public static function destroy($ids)
    {
        try {
            return parent::whereIn('id', (array) $ids)->delete();
        } catch (QueryException $e) {
            $this->queryExceptionResponse($e);
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

        if (request()->ajax()) {
            $response = response()->json(fill_db_data($e->errorInfo[1], $parameters));
        } else {
            $response = redirect()->back()
                                  ->with('alert', fill_db_data($e->errorInfo[1], $parameters))
                                  ->withInput();
        }

        throw new HttpResponseException($response);
    }

    /**
     * Add an "order by" primary key asc clause to the query.
     *
     * @return \Models\Builder\Builder
     */
    public function orderAsc()
    {
        return $this->orderBy($this->getKeyName(), 'asc');
    }

    /**
     * Add an "order by" primary key desc clause to the query.
     *
     * @return \Models\Builder\Builder
     */
    public function orderDesc()
    {
        return $this->orderBy($this->getKeyName(), 'desc');
    }
}
