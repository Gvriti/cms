<?php

namespace App\Http\Controllers\Site\Auth;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\AuthController as Controller;

class SiteAuthController extends Controller
{
    use ThrottlesLogins {
        sendLockoutResponse as lockoutResponse;
    }

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
     * {@inheritdoc}
     */
    protected function sendLockoutResponse(Request $request)
    {
        // Override "ThrottlesLogins" trait method "sendLockoutResponse".
        $response = $this->lockoutResponse($request);

        if ($request->ajax()) {
            return $this->sendAjaxLockoutResponse();
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function url($path = null)
    {
        if (is_null($path) || $path == '/') {
            return site_url();
        }

        return site_route($path);
    }
}
