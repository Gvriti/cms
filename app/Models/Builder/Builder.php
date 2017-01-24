<?php

namespace Models\Builder;

use Models\Abstracts\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * The columns that should be added to the count query.
     *
     * @var array
     */
    public $columnsForCount = [];

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
     * Add a new select to the count query.
     *
     * @param  array|mixed  $columns
     * @param  string  $method
     * @return \Models\Builder\Builder
     */
    public function addSelectCount($columns, $method = 'addSelect')
    {
        $columns = (array) $columns;

        if (! isset($this->columnsForCount[$method])) {
            $this->columnsForCount[$method] = [];
        }

        $this->columnsForCount[$method] = array_unique(array_merge(
            $this->columnsForCount[$method], $columns
        ));

        return call_user_func_array([$this, $method], $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function get($columns = ['*'])
    {
        $this->prefixColumnsOnJoin($columns);

        return parent::get($columns);
    }

    /**
     * Execute the query as a "select" statement or throw an exception.
     *
     * @param  array  $columns
     * @return \Illuminate\Support\Collection
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getOrFail($columns = ['*'])
    {
        $collection = $this->get($columns);

        $collection->isEmpty() and abort(404);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->prefixColumnsOnJoin($columns);

        if ($this->columnsForCount) {
            $columnsBackup = $this->query->columns;

            $this->query->columns = null;

            $query = $this->query->selectRaw('count(*) as aggregate');

            foreach ($this->columnsForCount as $method => $selects) {
                foreach ((array) $selects as $select) {
                    $query->{$method}($select);
                }
            }

            $total = $query->get();

            if (isset($this->query->groups)) {
                $total = count($total);
            } else {
                $total = isset($total[0]) ? $total[0]->aggregate : 0;
            }

            $this->query->columns = $columnsBackup;

            $this->forPage(
                $page = $page ?: Paginator::resolveCurrentPage($pageName),
                $perPage = $perPage ?: $this->model->getPerPage()
            );

            return new LengthAwarePaginator($this->get($columns), $total, $perPage, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]);
        }

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
     * @param  array  $columns
     * @return void
     */
    protected function prefixColumnsOnJoin($columns = ['*'])
    {
        $query = $this->getQuery();

        if (! is_null($joins = $query->joins)) {
            if (isset($columns[0]) && $columns[0] != '*') {
                $query->columns = (array) $columns;
            }

            $bindings = ['column'] + array_diff(array_keys($query->getRawBindings()), [
                // exclude from bindings
                'select'
            ]);

            foreach ($bindings as $i => $binding) {
                if (! is_array($binding = $query->{$binding . 's'})) {
                    continue;
                }

                foreach ($binding as $bind => $value) {
                    if ($value instanceof JoinClause) {
                        $wheres = $value->wheres;

                        foreach ($wheres as $key => $clause) {
                            if (! empty($clause['nested'])) continue;

                            if (is_null($clause['operator'])) {
                                $value->wheres[$key]['operator'] = "=";
                            }

                            if (isset($value->wheres[$key]['first'])
                                && strpos($first = $value->wheres[$key]['first'], '.') === false
                            ) {
                                $value->wheres[$key]['first'] = "{$query->from}.{$first}";
                            }

                            if (! empty($value->wheres[$key]['second'])
                                && is_string($second = $value->wheres[$key]['second'])
                                && strpos($second, '.') === false
                            ) {
                                $value->wheres[$key]['second'] = "{$value->table}.{$second}";
                           } elseif (is_null($value->wheres[$key]['second'])) {
                                $value->wheres[$key]['second'] = "{$value->table}.id";
                            }
                        }
                    } elseif (is_string($value) && $value == 'id') {
                        $query->{$bindings[$i] . 's'}[$bind] = $query->from . '.' . $value;
                    } elseif (is_array($value)
                            && isset($value['column'])
                            && strpos($value['column'], '.') === false
                        ) {
                        $columns = array_merge(
                            (array) array_values($this->model->getFillable()),
                            (array) array_values($this->model->getDates())
                        );

                        if ($value['column'] == 'id' || in_array($value['column'], $columns)) {
                            $table = $query->from . '.';

                            $query->{$bindings[$i] . 's'}[$bind]['column'] = $table . $value['column'];
                        }
                    }
                }
            }
        }
    }

    /**
     * Determine if any rows exist for the current query or throw an exception.
     *
     * @return bool
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function existsOrFail()
    {
        return $this->exists() || abort(404);
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

    /**
     * Add an "order by" created at asc clause to the query.
     *
     * @return \Models\Builder\Builder
     */
    public function createdAsc()
    {
        return $this->orderBy('created_at', 'asc');
    }

    /**
     * Add an "order by" created at desc clause to the query.
     *
     * @return \Models\Builder\Builder
     */
    public function createdDesc()
    {
        return $this->orderBy('created_at', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);

        $isLoop = in_array($method, [
            $backtrace[2]['function'], $backtrace[3]['function'], $backtrace[4]['function']
        ]);

        if (method_exists($this->model, $method) && ! $isLoop) {

            $this->model->setBuilder($this);

            return call_user_func_array([$this->model, $method], $parameters);
        }

        $result = call_user_func_array([$this->query, $method], $parameters);

        return in_array($method, $this->passthru) ? $result : $this;
    }
}
