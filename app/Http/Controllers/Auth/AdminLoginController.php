<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController as Controller;

class AdminLoginController extends Controller
{
    /**
     * The guard name.
     *
     * @var string
     */
    protected $guard = 'cms';

    /**
     * The path to the login view.
     *
     * @return string
     */
    protected $loginView = 'admin.auth.login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('cms.guest', ['except' => ['logout', 'setLockscreen']]);
    }

    /**
     * {@inheritdoc}
     */
    public function showLoginForm()
    {
        return view($this->loginView);
    }

    /**
     * {@inheritdoc}
     */
    public function username()
    {
        return $this->username ?: 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect(cms_route('login'));
    }

    /**
     * {@inheritdoc}
     */
    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    /**
     * {@inheritdoc}
     */
    public function redirectPath()
    {
        if ($this->redirectNamed) {
            return cms_route($this->redirectTo);
        }

        return cms_url($this->redirectTo);
    }

    /**
     * Get the lockscreen response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLockscreen(Request $request)
    {
        $request->session()->flash('includeLockscreen', 1);

        return view('admin.app');
    }

    /**
     * Set the lockscreen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function setLockscreen(Request $request)
    {
        Auth::guard($this->getGuard())->user()->lockScreen();

        if ($request->expectsJson()) {
            return response()->json([
                'result' => true, 'view' => view('admin.lockscreen')->render()
            ]);
        }

        return redirect(cms_route('dashboard'));
    }

    /**
     * Handle a lockscreen request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function postLockscreen(Request $request)
    {
        $isValid = false;

        if ($request->has('password')) {
            $isValid = Auth::guard($this->getGuard())->getProvider()->validateCredentials(
                Auth::guard($this->getGuard())->user(),
                $request->only('password')
            );
        }

        if ($isValid) {
            Auth::guard($this->getGuard())->user()->unlockScreen();

            if ($request->expectsJson()) {
                return response()->json(fill_data(true));
            }

            return redirect()->intended(cms_route('dashboard'));
        }

        if ($request->expectsJson()) {
            return response()->json(fill_data(false, trans('auth.invalid.password')));
        }

        return redirect()->back()->withErrors(trans('auth.invalid.password'));
    }
}
