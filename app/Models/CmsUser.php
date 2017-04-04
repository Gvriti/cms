<?php

namespace Models;

use Illuminate\Http\Request;
use Models\Abstracts\User as Model;
use Illuminate\Http\Exceptions\HttpResponseException;

class CmsUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cms_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'firstname', 'lastname', 'phone', 'address', 'role', 'active', 'photo', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = [];

    /**
     * Get the mutated "role" attribute.
     *
     * @return string
     */
    public function getRoleTextAttribute()
    {
        if (! is_null($this->role)) {
            return user_roles($this->role);
        }

        return $this->role;
    }

    /**
     * Get the mutated "photo" attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getPhotoAttribute($value)
    {
        return $value ?: asset('assets/images/user-2.png');
    }

    /**
     * Determine if the user is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role == 'admin';
    }

    /**
     * Set the lockscreen.
     *
     * @param  bool  $forceLock
     * @return \Illuminate\Session\Store
     */
    public function lockScreen($forceLock = false)
    {
        $lockscreen = session()->set('lockscreen', 1);

        if ($forceLock) {
            throw new HttpResponseException(redirect(cms_route('lockscreen')));
        }

        return $lockscreen;
    }

    /**
     * Determine if screen is locked.
     *
     * @return \Illuminate\Session\Store
     */
    public function hasLockScreen()
    {
        return session()->has('lockscreen');
    }

    /**
     * Remove the lockscreen.
     *
     * @return \Illuminate\Session\Store
     */
    public function unlockScreen()
    {
        return session()->remove('lockscreen');
    }

    /**
     * Filter a query by specific parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Models\Builder\Builder|static
     */
    public function adminFilter(Request $request)
    {
        $query = $this;

        if ($value = $request->get('name')) {
            $query = $query->whereRaw("CONCAT(firstname, ' ', lastname) like ?", ["%{$value}%"]);
        }

        if ($value = $request->get('email')) {
            $query = $query->whereRaw('email like ?', ["%{$value}%"]);
        }

        if ($value = $request->get('role')) {
            $query = $query->where('role', $value);
        }

        return $query;
    }
}
