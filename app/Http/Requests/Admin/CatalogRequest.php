<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CatalogRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('catalog');

        return [
            'title' => 'required|min:2',
            'slug' => 'required|min:2|unique:catalog,slug,'.$id,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all($keys = null)
    {
        $input = parent::all();

        $this->slugifyInput($input, 'slug', 'title');

        $input['visible'] = (int) $this->filled('visible');

        return $input;
    }
}
