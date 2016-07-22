<?php

namespace Models\Traits;

use Models\File;

trait FileableTrait
{
    /**
     * Get the model files.
     *
     * @param  int|null  $id
     * @param  string|null  $name
     * @return \Illuminate\Support\Collection
     */
    public function getFiles($id = null, $name = null)
    {
        $imageExt = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];

        $files = (new File)->joinLanguages()
                        ->byRoute($id ?: $this->id, $name ?: $this->getTable())
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
     * Add a "file" join to the query.
     * 
     * @return \Models\Builder\Builder
     */
    public function joinFileId()
    {
        $table = $this->getTable();

        $keyName = $this->getKeyName();

        $fileTable = (new File)->getTable();

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
        $file = (new File)->where([
            'route_id' => $id, 'route_name' => $this->table
        ])->first();

        return ! is_null($file);
    }
}
