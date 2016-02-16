<?php

namespace App\Http\Controllers;

use BadMethodCallException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;

abstract class AuthController extends Controller
{
    /**
     * The login username.
     *
     * @return string
     */
    protected $loginUsername = 'email';

    /**
     * The login username validation rules.
     *
     * @return string
     */
    protected $loginUsernameRules = 'required|email';

    /**
     * The path to the login route / url.
     *
     * @return string
     */
    protected $loginPath;

    /**
     * The path to the logout route / url.
     *
     * @return string
     */
    protected $logoutPath = '/';

    /**
     * The path to the login view.
     *
     * @return string
     */
    protected $loginView = 'auth.login';

    /**
     * The path to the registration view.
     *
     * @return string
     */
    protected $registerView = 'auth.register';

    /**
     * The path to the login view for ajax response.
     *
     * @return string
     */
    protected $ajaxLoginViewResponse;

    /**
     * The path to the logout view for ajax response.
     *
     * @return string
     */
    protected $ajaxLogoutViewResponse;

    /**
     * The URL for the authenticated user.
     *
     * @return string
     */
    protected $authenticatedPath = '/';

    /**
     * Get the failed login message translation.
     *
     * @return string
     */
    protected $authFailMessage = 'auth.failed';

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

    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @return string
     */
    abstract protected function url($path = null);

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        return view($this->loginView);
    }

    /**
     * Handle a login request to the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLogin()
    {
        $this->validate($this->request, [
            $this->loginUsername() => $this->loginUsernameRules, 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($this->request)) {
            return $this->sendLockoutResponse($this->request);
        }

        $credentials = $this->request->only($this->loginUsername(), 'password');

        if ($this->auth->attempt($credentials, $this->request->has('remember'))) {
            return $this->handleUserWasAuthenticated($throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($this->request);
        }

        if ($this->request->ajax()) {
            if ($this->request->wantsJson()) {
                return response()->json([
                    $this->loginUsername() => trans($this->authFailMessage)
                ], 422);
            }

            return response(trans($this->authFailMessage), 422);
        }

        $redirect = is_null($this->loginPath) ? redirect()->back() : redirect($this->loginPath());

        return $redirect->withInput($this->request->only($this->loginUsername(), 'remember'))
                        ->withErrors([$this->loginUsername() => trans($this->authFailMessage)]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return view($this->registerView);
    }

    /**
     * Handle a registration request for the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function postRegister()
    {
        $validator = $this->validator();

        if (! is_null($validator) && $validator->fails()) {
            $this->throwValidationException(
                $this->request, $validator
            );
        }

        $this->auth->login($this->create());

        return $this->handleUserWasAuthenticated();
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getLogout()
    {
        if ($this->request->ajax()) {
            if (! is_null($view = $this->ajaxLogoutViewResponse)) {
                $view = view($this->ajaxLogoutViewResponse);
            }

            if ($this->request->wantsJson()) {
                return response()->json(['result' => true, 'view' => $view->render()]);
            }

            return $view;
        }

        return is_null($this->logoutPath) ? redirect()->back()
                                          : redirect($this->logoutPath());
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated($throttles = false)
    {
        if ($throttles) {
            $this->clearLoginAttempts($this->request);
        }

        if ($this->request->ajax()) {
            return $this->ajaxViewResponse($this->ajaxLoginViewResponse);
        }

        if (is_null($this->authenticatedPath)) {
            return redirect()->back();
        }

        return redirect()->intended($this->url($this->authenticatedPath));
    }

    /**
     * Send the "Ajax" response after the user was authenticated.
     *
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function ajaxViewResponse($view = null)
    {
        if (! is_null($view)) {
            $view = view($view);
        }

        if ($this->request->wantsJson()) {
            return response()->json(['result' => true, 'view' => $view->render()]);
        }

        return $view;
    }

    /**
     * Get the path to the login route / url.
     *
     * @return string
     */
    public function loginPath()
    {
        return $this->url($this->loginPath);
    }

    /**
     * Get the path to the logout route / url.
     *
     * @return string
     */
    public function logoutPath()
    {
        return $this->url($this->logoutPath);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return $this->loginUsername;
    }

    /**
     * Determine if the class is using the ThrottlesLogins trait.
     *
     * @return bool
     */
    protected function isUsingThrottlesLoginsTrait()
    {
        return in_array(
            ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );
    }

    /**
     * Return a new JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendAjaxLockoutResponse()
    {
        $session = $this->request->getSession();

        if (! is_null($error = $session->get('errors'))) {
            $error = $error->first();
        }

        $session->forget('errors');

        if ($this->request->wantsJson()) {
            return response()->json(fill_data(false, $error), 429);
        }

        return response($error, 429);
    }
}
