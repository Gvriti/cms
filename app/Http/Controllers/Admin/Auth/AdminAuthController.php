<?php

namespace App\Http\Controllers\Admin\Auth;

use Custom\Auth\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\AuthController as Controller;

class AdminAuthController extends Controller
{
    use ThrottlesLogins;

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
    protected $loginView = 'admin.auth.login';

    /**
     * The URL for the authenticated user.
     *
     * @return string
     */
    protected $authenticatedPath = 'dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Custom\Auth\Auth  $auth
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Auth $auth, Request $request)
    {
        $this->middleware('CmsGuest', ['except' => ['getLogout', 'setLockscreen']]);

        $this->auth = $auth->cms();

        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogout()
    {
        if ($this->auth->check()) {
            $this->auth->get()->unlockScreen();
        }

        return parent::getLogout();
    }

    /**
     * Get the lockscreen response.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLockscreen()
    {
        if (! $this->auth->check()) {
            return redirect($this->loginPath());
        }

        $this->request->session()->flash('includeLockscreen', 1);

        return view('admin.app');
    }

    /**
     * Set the lockscreen.
     *
     * @return \Illuminate\Http\Response
     */
    public function setLockscreen()
    {
        $this->auth->get()->lockScreen();

        if ($this->request->ajax()) {
            $view = view()->make('admin.lockscreen')->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('dashboard'));
    }

    /**
     * Handle a lockscreen request to the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLockscreen()
    {
        $isValid = false;

        if ($this->request->has('password')) {
            $isValid = $this->auth->getProvider()->validateCredentials(
                $this->auth->get(),
                $this->request->only('password')
            );
        }

        if ($isValid) {
            $this->auth->get()->unlockScreen();

            if ($this->request->ajax()) {
                return response()->json(fill_data(true));
            }

            return redirect()->intended(cms_route('dashboard'));
        }

        if ($this->request->ajax()) {
            return response()->json(fill_data(false, trans('auth.invalid.password')));
        }

        return redirect()->back()->withErrors(trans('auth.invalid.password'));
    }

    /**
     * {@inheritdoc}
     */
    protected function url($path = null)
    {
        if (is_null($path) || $path == '/') {
            return cms_url();
        }

        return cms_route($path);
    }
}
