<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class VideoRequest extends Request
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
    public function validationData()
    {
        $input = parent::validationData();

        $input['visible'] = $this->has('visible') ? 1 : 0;

        return $input;
    }
}
