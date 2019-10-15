<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use Cocur\Slugify\Slugify;

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
            'title' => 'required|min:2',
            'slug' => 'required|min:2|unique:articles,slug,'.$id,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all($keys = null)
    {
        $input = parent::all();

        if ($this->filled('slug')) {
            $input['slug'] = (new Slugify)->slugify($input['slug']);
        } else {
            $input['slug'] = (new Slugify)->slugify($this->get('title'));
        }

        $input['visible'] = (int) $this->filled('visible');

        if (! $this->filled('created_at')) {
            unset($input['created_at']);
        }

        return $input;
    }
}
