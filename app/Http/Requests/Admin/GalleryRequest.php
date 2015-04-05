<?php

namespace App\Http\Requests\Admin;

use Cocur\Slugify\Slugify;
use App\Http\Requests\Request;

class GalleryRequest extends Request
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
        $id = $this->route('galleries');

        return [
            'title'          => 'required|min:2',
            'short_title'    => 'required|min:2',
            'slug'           => 'required|min:2|unique:galleries,slug,'.$id,
            'type'           => 'required',
            'admin_order_by' => 'required',
            'admin_sort'     => 'required',
            'admin_per_page' => 'required|numeric|min:2|max:50',
            'site_order_by'  => 'required',
            'site_sort'      => 'required',
            'site_per_page'  => 'required|numeric|min:2|max:50'
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

        $request = $this->request;

        if (! $this->has('short_title')) {
            $input['short_title'] = $this->get('title');
        }

        if ($this->has('slug')) {
            $input['slug'] = (new Slugify)->slugify($input['slug']);
        } else {
            $input['slug'] = (new Slugify)->slugify($this->get('title'));
        }

        if (! array_key_exists($this->get('type'), gallery_types())) {
            $input['type'] = null;
        }

        $orderList = gallery_order();

        if (! array_key_exists($this->get('admin_order_by'), $orderList)) {
            $input['admin_order_by'] = null;
        }

        if (! array_key_exists($this->get('site_order_by'), $orderList)) {
            $input['site_order_by'] = null;
        }

        $sortList = gallery_sorts();

        if (! array_key_exists($this->get('admin_sort'), $sortList)) {
            $input['admin_sort'] = null;
        }

        if (! array_key_exists($this->get('site_sort'), $sortList)) {
            $input['site_sort'] = null;
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
