<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

abstract class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * The login username.
     *
     * @return string
     */
    protected $username = 'email';

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectPath = '/';

    /**
     * The maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected $maxLoginAttempts = 3;

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected $lockoutTime = 120;
}
