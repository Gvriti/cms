<?php

namespace Models;

use Models\Abstracts\Model;
use Models\Traits\LanguageTrait;
use Models\Traits\PositionableTrait;
use Illuminate\Filesystem\Filesystem;

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
    protected $fillable = [
        'route_name', 'route_id', 'position', 'visible'
    ];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [
        'route_name', 'route_id'
    ];

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
    protected $languageFillable = [
        'file_id', 'language', 'title', 'file'
    ];

    /**
     * The attributes that are not updatable for the Language model.
     *
     * @var array
     */
    protected $languageNotUpdatable = [
        'file_id', 'language'
    ];

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
    protected $foreignModel;

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! is_null($route = request()->route())) {
            if (! is_null($routeName = $route->parameter('routeName'))) {
                $this->setAttribute('route_name', $routeName);
            }

            if (! is_null($routeId = $route->parameter('routeId'))) {
                $this->setAttribute('route_id', $routeId);
            }
        }
    }


    /**
     * Get the specified Eloquent model instance.
     *
     * @return \Models\Abstracts\Model
     */
    public function makeForeign()
    {
        if (! is_null($this->foreignModel)) {
            return $this->foreignModel;
        }

        $namespace = __NAMESPACE__ . '\\';
        $model = $namespace . ($name = str_singular(ucfirst($this->route_name)));

        if (! class_exists($model)) {
            $modelExists = false;

            if (! empty($dirs = (new Filesystem)->directories(app_path('Models')))) {
                foreach ($dirs as $dir) {
                    if (($baseName = basename($dir)) == 'Abstracts') {
                        continue;
                    }

                    $model = $namespace . $baseName . '\\' . $name;

                    if (class_exists($model)) {
                        $modelExists = true;

                        break;
                    }
                }
            }

            if (! $modelExists) abort(404);
        }

        $this->foreignModel = new $model;

        $this->foreignModel = $this->foreignModel
                                    ->joinLanguages()
                                    ->findOrFail($this->route_id);

        $this->foreignModel['routeName'] = $this->route_name;

        $type = (array) cms_files($this->route_name);

        if (isset($type['foreign_key'])) {
            $routeParams[] = $this->foreignModel->{$type['foreign_key']};
        }

        $routeParams[] = $this->foreignModel->id;

        $this->foreignModel['routeParams'] = $routeParams;

        return $this->foreignModel;
    }

    /**
     * Get the files by route.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByRoute()
    {
        return $this->joinLanguages()
                    ->byRoute()
                    ->orderBy('position', 'desc')
                    ->paginate(20);
    }

    /**
     * Add a where `route_id, route_name` clause to the query.
     *
     * @param  null|int     $routeId
     * @param  null|string  $routeName
     * @return \Models\Abstracts\Builder
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
     * @return \Models\Abstracts\Builder
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

        return parent::create($attributes);
    }
}
