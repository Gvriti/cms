<?php

namespace Models\Traits;

use Models\File;

trait FileableTrait
{
    /**
     * Get the model files.
     *
     * @param  bool  $separated
     * @param  array|mixed  $columns
     * @return \Illuminate\Support\Collection
     */
    public function getFiles($separated = true, $columns = ['*'])
    {
        $imageExt = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];

        $files = (new File)->joinLanguage()
            ->byForeign($this->getTable(), $this->getKey())
            ->whereVisible()
            ->positionDesc()
            ->get($columns);

        if (! $separated) {
            return $files;
        }

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
     * Add a files count to the query.
     *
     * @return \Models\Builder\Builder
     */
    public function countFiles()
    {
        return $this->selectSub(function ($q) {
            $tableId = ($table = $this->getTable()).'.'.$this->getKeyName();

            return $q->from((new File)->getTable())
                ->whereColumn('table_id', $tableId)
                ->where('table_name',  $table)
                ->selectRaw('count(*)');
        }, 'files_count');
    }

    /**
     * Determine if the model has a file(s).
     *
    * @return \Models\Builder\Builder
     */
    public function hasFile()
    {
        return $this->selectExists(function ($q) {
            $tableId = ($table = $this->getTable()).'.'.$this->getKeyName();

            return $q->from((new File)->getTable())
                ->whereColumn('table_id', $tableId)
                ->where('table_name', $table);
        }, 'has_file');
    }

    /**
     * Add query where file exists or not.
     *
     * @param  bool  $exists
     * @return \Models\Builder\Builder
     */
    public function whereFileExists($exists = true)
    {
        return $this->whereExists(function ($q) {
            $tableId = ($table = $this->getTable()).'.'.$this->getKeyName();

            return $q->from((new File)->getTable())
                ->whereColumn('table_id', $tableId)
                ->where('table_name', $table);
        }, 'and', ! $exists);
    }
}
