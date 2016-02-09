<?php

namespace Models\Traits;

use Models\File;
use Models\Abstracts\Model;

trait FileableTrait
{
    /**
     * Get the File instance.
     *
     * @return \Models\File
     */
    public function file()
    {
        return new File;
    }

    /**
     * Get the model files.
     *
     * @param  int  $id
     * @return \Illuminate\Support\Collection|static[]
     */
    public function getFiles($id = null)
    {
        $imageExt = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];

        $files = $this->file()->joinLanguages()
                              ->byRoute($id ?: $this->id, $this->getTable())
                              ->visible()
                              ->positionDesc()
                              ->get();

        $images = $mixed = [];

        if (! $files->isEmpty()) {
            foreach ($files as $key => $value) {
                $item = $files->pull($key);

                if (in_array(strtolower(pathinfo($item->file, PATHINFO_EXTENSION)), $imageExt)) {
                    $images[] = $item;
                } else {
                    $mixed[] = $item;
                }
            }
        }

        $files->put('images', $images);
        $files->put('mixed', $mixed);

        return $files;
    }

    /**
     * Add a `file` left join to the query.
     * 
     * @return \Models\Abstracts\Builder
     */
    public function joinFileId()
    {
        $table = $this->getTable();

        $keyName = $this->getKeyName();

        $fileTable = $this->file()->getTable();

        return $this->leftJoin($fileTable, function ($join) use ($table, $keyName, $fileTable) {
            $join->on("{$table}.{$keyName}", '=', "route_id")
                 ->where("{$fileTable}.route_name", '=', $table);
        })->groupBy("{$table}.{$keyName}")->addSelect(["{$fileTable}.id as {$fileTable}_id"]);
    }

    /**
     * Determine if the model has a file(s).
     * 
     * @param  int  $id
     * @return bool
     */
    public function hasFile($id)
    {
        $file = $this->file()->where(['route_id' => $id, 'route_name' => $this->table])->first();

        return ! is_null($file);
    }
}
