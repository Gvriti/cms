<?php

namespace App\Http\Controllers\Site\Auth;

use Custom\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\AuthController as Controller;

class SiteAuthController extends Controller
{
    use ThrottlesLogins {
        sendLockoutResponse as lockoutResponse;
    }

    /**
     * The Auth implementation.
     *
     * @var \Custom\Auth\Auth
     */
    protected $auth;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The path to the login view.
     *
     * @return string
     */
    protected $loginView = 'site.auth.login';

    /**
     * The path to the registration view.
     *
     * @return string
     */
    protected $registerView = 'site.auth.register';

    /**
     * The path to the login view for ajax response.
     *
     * @return string
     */
    protected $ajaxLoginViewResponse = 'site.auth.mini_profile';

    /**
     * The path to the logout view for ajax response.
     *
     * @return string
     */
    protected $ajaxLogoutViewResponse = 'site.auth.login';

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Custom\Auth\Auth  $auth
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Auth $auth, Request $request)
    {
        $this->middleware('SiteGuest', ['except' => ['getLogout']]);

        $this->auth = $auth;

        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogout()
    {
        $this->auth->user()->logout();

        if ($this->request->ajax()) {
            if (! is_null($html = $this->ajaxLogoutViewResponse)) {
                $html = view()->make($this->ajaxLogoutViewResponse)->render();
            }

            return response()->json(['result' => true, 'view' => $html]);
        }

        return is_null($this->loginPath) ? redirect()->back()
                                         : redirect($this->loginPath());
    }

    /**
     * {@inheritdoc}
     */
    protected function sendLockoutResponse(Request $request)
    {
        // Overridden "ThrottlesLogins" trait method
        $response = $this->lockoutResponse($request);

        if ($this->request->ajax()) {
            return $this->sendAjaxLockoutResponse();
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function url($path = null)
    {
        return site_url($path);
    }
}
