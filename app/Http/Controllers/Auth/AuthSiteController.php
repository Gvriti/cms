<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Models\User;
use App\Http\Controllers\Auth\AuthController as Controller;

class AuthSiteController extends Controller
{
    /**
     * The path to the login view.
     *
     * @return string
     */
    protected $loginView = 'site.auth.login';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email'     => 'required|email|max:255|unique:users',
            'firstname' => 'required|min:2|max:255',
            'lastname'  => 'required|min:2|max:255',
            'password'  => 'required|min:6|confirmed',
        ], [], (array) trans('attributes'));
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Models\User
     */
    protected function create()
    {
        $input = request()->only([
            'email', 'firstname', 'lastname', 'password'
        ]);

        $input['password'] = bcrypt($input['password']);

        return (new User)->create($input);
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (is_null($this->redirectPath) || $this->redirectPath == '/') {
            return site_url();
        }

        return site_route($this->redirectPath);
    }
}
