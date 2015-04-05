<?php

namespace Models;

use Models\Abstracts\Model;
use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;

class File extends Model
{
    use LanguageTrait, PositionableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['route_name', 'route_id', 'position', 'visible'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = ['route_name', 'route_id', 'position'];

    /**
     * Related database table name used by the Language model.
     *
     * @var string
     */
    protected $languageTable = 'file_languages';

    /**
     * The attributes that are mass assignable for the Language model.
     *
     * @var array
     */
    protected $languageFillable = ['file_id', 'language', 'title', 'file'];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = ['language'];

    /**
     * Get the mutated file attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getFileDefaultAttribute($value)
    {
        return $value ?: asset('assets/images/album-img-1.png');
    }

    /**
     * The Model instance, passed by the route.
     *
     * @var \Models\Abstracts\Model
     */
    protected $routeModel;

    /**
     * The route name of the Eloquent model.
     *
     * @var string
     */
    public $route_name;

    /**
     * The route id of the Eloquent model.
     *
     * @var int
     */
    public $route_id;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->language($this);

        if (! is_null($route = request()->route())) {
            $this->route_name = $route->parameter('routeName');

            $this->route_id = $route->parameter('routeId');
        }
    }


    /**
     * Get the specified Eloquent model instance.
     *
     * @return \Models\Abstracts\Model
     */
    public function makeRoute()
    {
        if (! is_null($this->routeModel)) {
            return $this->routeModel;
        }

        $model = __NAMESPACE__ . '\\' . str_singular(ucfirst($this->route_name));

        if (is_null($type = file_types($this->route_name)) || ! class_exists($model)) {
            abort(404);
        }

        $this->routeModel = new $model;

        $this->routeModel = $this->routeModel->joinLanguages()
                                             ->currentLanguage()
                                             ->findOrFail($this->route_id);

        $this->routeModel['routeName'] = $this->route_name;
        $this->routeModel['foreignKey'] = $this->routeModel->{$type['foreign_key']};

        return $this->routeModel;
    }

    /**
     * Get the files by route.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByRoute()
    {
        return $this->joinLanguages()->byRoute()->currentLanguage()
                                                ->orderBy('position', 'desc')
                                                ->paginate(20);
    }

    /**
     * Add a where `route_id, route_name` clause to the query.
     *
     * @param  int|null     $routeId
     * @param  string|null  $routeName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function byRoute($routeId = null, $routeName = null)
    {
        return $this->where('route_id', $routeId ?: $this->route_id)
                    ->where('route_name', $routeName ?: $this->route_name);
    }

    /**
     * Add a where `visible` clause to the query.
     *
     * @param  int  $visible
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function visible($visible = 1)
    {
        return $this->where('visible', (int) $visible);
    }

    /**
     * Save a new model and get the instance.
     *
     * @param  array  $attributes
     * @return $this
     */
    public static function create(array $attributes = [])
    {
        $attributes['position'] = (int) parent::byRoute()->max('position') + 1;

        $model = parent::create($attributes);

        $model->createLanguage($attributes);

        return $model;
    }
}
