<?php

namespace App\Http\Requests\Admin;

use Cocur\Slugify\Slugify;
use App\Http\Requests\Request;

class PageRequest extends Request
{
    /**
     * Determine if the user is authorized to make equest.
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
        $id = $this->route('pages');

        return [
            'title'         => 'required|min:2',
            'short_title'   => 'required|min:2',
            'slug'          => 'required|min:2|unique:pages,slug,'.$id,
            'type'          => 'required',
            'collection_id' => 'required_if:type,collection|integer'
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

        if (! $this->has('short_title')) {
            $input['short_title'] = $this->get('title');
        }

        if ($this->has('slug')) {
            $input['slug'] = (new Slugify)->slugify($input['slug']);
        } else {
            $input['slug'] = (new Slugify)->slugify($this->get('title'));
        }

        if (! array_key_exists($this->get('type'), page_types())) {
            $input['type'] = null;
        }

        if ($input['type'] != 'collection') {
            $input['collection_id'] = 0;
        }

        if (! $this->has('meta_desc')) {
            $input['meta_desc'] = text_limit($this->get('description') ?: $this->get('content'));
        }

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
