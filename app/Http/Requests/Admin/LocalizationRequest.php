<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class LocalizationRequest extends Request
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
        $id = $this->route('localization');

        return [
            'name'  => 'required|min:2|max:32|regex:/^\w+$/|unique:localization,name,'.$id,
            'title' => 'required|min:2',
            'value' => 'required|min:2'
        ];
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
