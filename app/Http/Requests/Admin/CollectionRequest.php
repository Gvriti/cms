<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CollectionRequest extends Request
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
        return [
            'title'          => 'required|min:2|max:250',
            'type'           => 'required',
            'admin_order_by' => 'required',
            'admin_sort'     => 'required',
            'admin_per_page' => 'required|numeric|min:1|max:50',
            'site_order_by'  => 'required',
            'site_sort'      => 'required',
            'site_per_page'  => 'required|numeric|min:1|max:50'
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

        if (! array_key_exists($this->get('type'), collection_types())) {
            $input['type'] = null;
        }

        $orderList = collection_order();

        if (! array_key_exists($this->get('admin_order_by'), $orderList)) {
            $input['admin_order_by'] = null;
        }

        if (! array_key_exists($this->get('site_order_by'), $orderList)) {
            $input['site_order_by'] = null;
        }

        $sortList = collection_sorts();

        if (! array_key_exists($this->get('admin_sort'), $sortList)) {
            $input['admin_sort'] = null;
        }

        if (! array_key_exists($this->get('site_sort'), $sortList)) {
            $input['site_sort'] = null;
        }

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
