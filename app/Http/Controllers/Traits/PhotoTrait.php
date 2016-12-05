<?php

namespace App\Http\Controllers\Traits;

use Exception;
use Models\Abstracts\User;
use League\Glide\ServerFactory as Glide;

trait PhotoTrait
{
    /**
     * Get the user photo.
     *
     * @param  int|null  $id
     * @param  string|null  $path
     * @return mixed
     */
    public function getPhoto($id = null, $path = null)
    {
        if (isset($this->user) && $this->user instanceof User) {
            $id = $this->user->id;

            $path = $this->user->getTable();
        }

        if (is_null($id) || is_null($path)) {
            return $this->getDefaultPhotoResponse();
        }

        try {
            (new Glide([
                'source' => storage_path("app/images/{$path}"),
                'cache'  => storage_path("app/images/cache/{$path}"),
                'group_cache_in_folders' => false
            ]))->getServer()->outputImage(
                "{$id}/photo.jpg", config('web.glide_users', [])
            );
        } catch (Exception $e) {}

        return $this->getDefaultPhotoResponse();
    }

    /**
     * Get the default photo response.
     *
     * @param  string  $path
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getDefaultPhotoResponse($path)
    {
        try {
            return response()->file(public_path('assets/images/user-2.png'));
        } catch (Exception $e) {
            abort(404);
        }
    }
}
