<?php

namespace Models\Traits;

use Models\File;

trait FileableTrait
{
    /**
     * Get the model files.
     *
     * @param  array|mixed  $columns
     * @param  int|null  $id
     * @param  string|null  $name
     * @return \Illuminate\Support\Collection
     */
    public function getFiles($columns = ['*'], $id = null, $name = null)
    {
        $imageExt = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];

        $files = (new File)->joinLanguages()
            ->byRoute($id ?: $this->id, $name ?: $this->getTable())
            ->visible()
            ->positionDesc()
            ->get($columns);

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

        return $this->selectRaw("(select count(*) from {$fileTable} where {$table}.{$keyName} = model_id and model_name = ?) as files_cnt", [$table]);
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
            'model_id' => $id, 'model_name' => $this->table
        ])->first();

        return ! is_null($file);
    }
}
