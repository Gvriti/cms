<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * The login username.
     *
     * @return string
     */
    protected $username = 'email';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Indicates if the redirect path is named route.
     *
     * @var bool
     */
    protected $redirectNamed = false;

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
