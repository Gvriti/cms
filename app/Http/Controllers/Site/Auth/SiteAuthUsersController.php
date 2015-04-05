<?php

namespace App\Http\Controllers\Site\Auth;

use Models\User;
use Custom\Auth\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Site\Auth\SiteAuthController as Controller;

class SiteAuthUsersController extends Controller
{
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
     * Create a new authentication controller instance.
     *
     * @param  \Custom\Auth\Auth  $auth
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Auth $auth, Request $request)
    {
        $this->auth = $auth->user();

        $this->request = $request;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator()
    {
        return $this->validate($this->request, [
            'email'     => 'required|email|max:255|unique:users',
            'firstname' => 'required|min:2|max:255',
            'lastname'  => 'required|min:2|max:255',
            'password'  => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \Models\User
     */
    protected function create()
    {
        $data = $this->request->only([
            'email', 'firstname', 'lastname', 'password'
        ]);

        return app(User::class)->create([
            'email'     => $data['email'],
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'password'  => bcrypt($data['password']),
        ]);
    }
}
