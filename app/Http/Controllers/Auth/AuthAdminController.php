<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController as Controller;

class AuthAdminController extends Controller
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
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('cms.guest', ['except' => ['logout', 'getLogout', 'setLockscreen']]);

        $this->redirectPath = cms_url();

        $this->redirectAfterLogout = cms_route('login');
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
     * @return \Illuminate\Http\Response
     */
    public function setLockscreen(Request $request)
    {
        Auth::guard($this->getGuard())->user()->lockScreen();

        if ($request->ajax()) {
            $view = view('admin.lockscreen')->render();

            return response()->json(['result' => true, 'view' => $view]);
        }

        return redirect(cms_route('dashboard'));
    }

    /**
     * Handle a lockscreen request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

            if ($request->ajax()) {
                return response()->json(fill_data(true));
            }

            return redirect()->intended(cms_route('dashboard'));
        }

        if ($request->ajax()) {
            return response()->json(fill_data(false, trans('auth.invalid.password')));
        }

        return redirect()->back()->withErrors(trans('auth.invalid.password'));
    }
}
