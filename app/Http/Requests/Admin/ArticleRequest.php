<?php

namespace App\Http\Requests\Admin;

use Cocur\Slugify\Slugify;
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

        $input['visible'] = $this->filled('visible') ? 1 : 0;

        if (! $this->filled('created_at')) {
            unset($input['created_at']);
        }

        return $input;
    }
}
