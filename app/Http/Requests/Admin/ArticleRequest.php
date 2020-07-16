<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class ArticleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('article');

        return [
            'slug' => 'required|min:2|unique:articles,slug,'.$id,
            'title' => 'required|min:2',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all($keys = null)
    {
        $input = parent::all();

        $this->slugifyInput($input, 'slug', ['title']);

        $input['visible'] = (int) $this->filled('visible');

        if (! $this->filled('created_at')) {
            unset($input['created_at']);
        }

        return $input;
    }
}
