<?php

namespace App\Http\Requests\Admin;

use Cocur\Slugify\Slugify;
use App\Http\Requests\Request;

class PageRequest extends Request
{
    /**
     * Determine if the user is authorized to make request.
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
        $id = $this->route('page');

        return [
            'title'       => 'required|min:2',
            'short_title' => 'required|min:2',
            'slug'        => 'required|min:2|unique:pages,slug,'.$id,
            'type'        => 'required',
            'type_id'     => 'required_if:type,' . $this->get('type') . '|integer'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $input = parent::all();

        if (! $this->has('short_title')) {
            $input['short_title'] = $this->get('title');
        }

        if ($this->has('slug')) {
            $input['slug'] = (new Slugify)->slugify($input['slug']);
        } else {
            $input['slug'] = (new Slugify)->slugify($this->get('title'));
        }

        if (! array_key_exists($type = $this->get('type'), cms_pages('types'))) {
            $input['type'] = null;
        }

        if (! in_array($type, cms_pages('attached'))) {
            $input['type_id'] = 0;
        }

        $input['visible'] = $this->has('visible') ? 1 : 0;

        return $input;
    }
}
