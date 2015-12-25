<?php

namespace Models\Builder;

use Models\Abstracts\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    /**
     * The Model instance.
     *
     * @var \Models\Abstracts\Model
     */
    protected $model;

    /**
     * Create a new Eloquent query builder instance.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  \Models\Abstracts\Model  $model
     * @return void
     */
    public function __construct(QueryBuilder $query, Model $model)
    {
        parent::__construct($query);

        $this->model = $model;
    }

    /**
     * Add a new select column to the query.
     *
     * @param  mixed  $column
     * @return $this
     */
    public function addSelect($columns)
    {
        $this->query->addSelect($columns);

        $query = $this->getQuery();

        $query->columns = array_unique($query->columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($columns = ['*'])
    {
        $this->prefixColumnsOnJoin();

        return parent::get($columns);
    }

    /**
     * Execute the query as a "select" statement or throw an exception.
     *
     * @param  array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getOrFail($columns = ['*'])
    {
        $collection = $this->get($columns);

        if (! $collection->isEmpty()) {
            return $collection;
        }

        abort(404);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->prefixColumnsOnJoin();

        return parent::paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * Update the model in the database.
     *
     * @param  array   $attributes
     * @param  string  $exclude
     * @return int
     */
    public function update(array $attributes = [], $exclude = null)
    {
        $attributes = $this->model->getUpdatable($attributes, $exclude);

        return parent::update($attributes);
    }

    /**
     * Prefix columns with the model table name if join clause is set.
     *
     * @return void
     */
    protected function prefixColumnsOnJoin()
    {
        $query = $this->getQuery();

        if (! is_null($joins = $query->joins)) {
            $bindings = array_diff(array_keys($query->getRawBindings()), [
                // exclude from prefix
                'select'
            ]);

            foreach ($bindings as $i => $binding) {
                if (! is_array($binding = $query->{$binding . 's'})) {
                    continue;
                }

                foreach ($binding as $key => $value) {
                    if ($value instanceof JoinClause) {
                        $clauses = $value->clauses;

                        foreach ($clauses as $key => $clause) {
                            if (is_null($clause['operator'])) {
                                $value->clauses[$key]['operator'] = "=";
                            }

                            if (strpos($first = $value->clauses[$key]['first'], '.') === false) {
                                $value->clauses[$key]['first'] = "{$query->from}.{$first}";
                            }

                            if (strpos($second = $value->clauses[$key]['second'], '.') === false) {
                                if (is_null($second)) {
                                    $value->clauses[$key]['second'] = "{$value->table}.id";
                                } else {
                                    $value->clauses[$key]['second'] = "{$value->table}.{$second}";
                                }
                            }
                        }
                    } elseif (isset($value['column']) && strpos($value['column'], '.') === false) {
                        if ($value['column'] == 'id'
                            || in_array($value['column'], $this->model->getFillable())
                        ) {
                            $table = $query->from . '.';
                        } else {
                            $table = null;
                        }

                        $query->{$bindings[$i] . 's'}[$key]['column'] = $table . $value['column'];
                    }
                }
            }
        }
    }

    /**
     * Dynamically handle calls into the query or Eloquent model instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->model, $method)) {
            $this->model->setBuilder($this);

            return call_user_func_array([$this->model, $method], $parameters);
        }

        $result = call_user_func_array([$this->query, $method], $parameters);

        return in_array($method, $this->passthru) ? $result : $this;
    }
}
