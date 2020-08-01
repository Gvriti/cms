<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class SliderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|min:2|max:250',
            'file' => 'required'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all($keys = null)
    {
        $input = parent::all();

        $input['visible'] = (int) $this->$this->boolifyInput($input, ['visible']);

        return $input;
    }
}
