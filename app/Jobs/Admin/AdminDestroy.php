<?php

namespace App\Jobs\Admin;

use Models\File;
use App\Jobs\Job;
use Models\Abstracts\Model;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Bus\SelfHandling;

class AdminDestroy extends Job implements SelfHandling
{
    /**
     * The Model instance.
     *
     * @var object
     */
    protected $model;

    /**
     * The id of the Model.
     *
     * @var int
     */
    protected $id;

    /**
     * Indicates if the model has a attached files.
     *
     * @return bool
     */
    protected $isFileable;

    /**
     * Create a new job instance.
     *
     * @param  Models\Model  $model
     * @param  int    $id
     * @param  bool   $isFileable
     * @return void
     */
    public function __construct($model, $id, $isFileable = true)
    {
        $this->model = $model;

        $this->id = $id;

        $this->isFileable = $isFileable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->isDeletable() || ! $this->performDelete()) {
            return $this->response('error', trans('database.error.1451'));
        }

        return $this->response('success', trans('database.deleted'));
    }

    /**
     * Determine if this Model is  deletable.
     *
     * @return Response|bool
     */
    protected function isDeletable()
    {
        if (! $this->model instanceof Model) {
            return false;
        }

        if ($this->isFileable) {
            $hasFiles = (new File)->byRoute($this->id, $this->model->getTable())
                                  ->first(['id']);

            if ($hasFiles) {
                return false;
            }
        }

        return true;
    }

    /**
     * Perform the delete query on this Model instance.
     *
     * @return bool|Illuminate\Database\QueryException
     */
    protected function performDelete()
    {
        if (is_array($this->id)) {
            return (bool) $this->model->destroy($this->id);
        }

        return (bool) $this->model->findOrFail($this->id)->delete();
    }

    /**
     * Check request type and return appropriate response
     *
     * @param  string  $type
     * @param  string  $message
     * @return Response
     */
    protected function response($type, $message)
    {
        if (request()->ajax()) {
            return response()->json(fill_data($type, $message));
        }

        return redirect()->back()->with('alert', fill_data($type, $message));
    }
}
