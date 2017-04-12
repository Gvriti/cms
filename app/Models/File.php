<?php

namespace Models;

use Exception;
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
        'model_name', 'model_id', 'position', 'visible'
    ];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [
        'model_name', 'model_id'
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
     * Get the mutated file default attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getFileDefaultAttribute($value)
    {
        return $value ?: asset('assets/images/album-img-1.png');
    }

    /**
     * The eloquent model instance.
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
            if (is_null($this->model_name)
                && ! is_null($modelName = $route->parameter('modelName'))
            ) {
                $this->setAttribute('model_name', snake_case($modelName));
            }

            if (is_null($this->model_id)
                && ! is_null($modelId = $route->parameter('modelId'))
            ) {
                $this->setAttribute('model_id', $modelId);
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
        $model = $namespace . ($name = str_singular(studly_case($this->model_name)));

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

        if ($this->foreignModel->hasLanguage()) {
            $this->foreignModel = $this->foreignModel->joinLanguage();
        }

        $this->foreignModel = $this->foreignModel->findOrFail($this->model_id);

        $type = (array) cms_files($this->model_name);

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
     * @param  int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByRoute($perPage = 20)
    {
        return $this->joinLanguage()
            ->byRoute()
            ->orderBy('position', 'desc')
            ->paginate($perPage);
    }

    /**
     * Add a where "model_id, model_name" clause to the query.
     *
     * @param  null|string  $modelName
     * @param  null|int     $modelId
     * @return \Models\Builder\Builder
     */
    public function byRoute($modelName = null, $modelId = null)
    {
        return $this->where('model_name', $modelName ?: $this->model_name)
            ->where('model_id', $modelId ?: $this->model_id);
    }

    /**
     * Add a where "visible" clause to the query.
     *
     * @param  int  $value
     * @return \Models\Builder\Builder
     */
    public function visible($value = 1)
    {
        return $this->where('visible', (int) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes = [])
    {
        $attributes['position'] = (int) parent::byRoute()->max('position') + 1;

        return parent::create($attributes);
    }

    /**
     * Get the file size.
     *
     * @param  string|null $file
     * @return string
     */
    public function getFileSize($file = null)
    {
        try {
            $size = (new Filesystem)->size(
                base_path(trim(parse_url($file ?: $this->file, PHP_URL_PATH), '/'))
            );
        } catch (Exception $e) {
            $size = 0;
        }

        return format_bytes($size);
    }
}
