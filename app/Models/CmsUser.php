<?php

namespace Models;

use Models\Abstracts\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class CmsUser extends Model implements AuthenticatableContract
{
    use Authenticatable;

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
    protected $fillable = ['email', 'firstname', 'lastname', 'phone', 'address', 'role', 'active', 'photo', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that are not updatable.
     *
     * @var array
     */
    protected $notUpdatable = ['remember_token'];

    /**
     * Get the mutated `role` attribute.
     *
     * @param  string  $value
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
     * Get the mutated `photo` attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getPhotoAttribute($value)
    {
        if (! $value) {
            $value = asset('assets/images/user-2.png');
        }

        return $value;
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
}
