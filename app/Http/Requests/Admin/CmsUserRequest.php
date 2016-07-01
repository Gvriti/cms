<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CmsUserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('cms_users');

        $passwordRequired = $this->method() == 'POST' ? 'required|' : '';

        return [
            'email'     => 'required|email|unique:cms_users,email,'.$id,
            'firstname' => 'required|min:2',
            'lastname'  => 'required|min:2',
            'phone'     => 'digits_between:3,32',
            'role'      => 'required',
            'password'  => $passwordRequired . 'min:6|confirmed'
        ];
    }

    /**
     * Override parent method, that contains all requests.
     *
     * @return array
     */
    public function all()
    {
        $input = parent::all();

        $id = $this->route('cms_users');

        $auth = $this->user('cms');

        if ($this->method() == 'PUT' && ! $auth->isAdmin()) {
            $input['role'] = null;
        } elseif (! in_array($this->get('role'), array_keys(user_roles()))) {
            $input['role'] = null;
        }

        if ($auth->id == $id) {
            $input['role'] = $auth->role;
        }

        if (! $this->has('password')) {
            unset($input['password']);
        } else {
            $this->offsetSet('password', bcrypt($input['password']));
        }

        $input['active'] = $this->has('active') ? 1 : 0;

        return $input;
    }

    /**
     * Set custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return (array) trans('attributes');
    }
}
