<?php

namespace Models;

use Models\Abstracts\Model;

class Permission extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cms_user_id', 'route_name'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [];

    /**
     * Get the list of permissions by user id.
     *
     * @param  int  $id
     * @return \Models\Abstracts\Builder
     */
    public function permissions($id)
    {
        return $this->where('cms_user_id', $id);
    }

    /**
     * Determine if the user has access to the given route.
     *
     * @param  string  $routeName
     * @return bool
     */
    public function accessRoute($routeName)
    {
        return is_null($this->where('route_name', $routeName)->first());
    }

    /**
     * Clear permissions from the database.
     *
     * @param  int  $id
     * @return bool|null
     */
    public function clear($id)
    {
        return $this->permissions($id)->delete();
    }
}
