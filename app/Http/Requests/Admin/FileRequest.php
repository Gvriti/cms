<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class FileRequest extends Request
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
        return [
            'title' => 'required|min:2|max:250',
            'file'  => 'required'
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

        $input['visible'] = $this->has('visible') ? 1 : 0;

        return $input;
    }

    /**
     * Set custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('attributes');
    }
}
