<?php

namespace App\Http\Requests;

use Cocur\Slugify\Slugify;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
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
     * {@inheritdoc}
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance()->after(function ($validator) {
            if (method_exists($this, 'after')) {
                $this->after($validator);
            }
        });

        if (method_exists($this, 'before')) {
            $this->before($validator);
        }

        return $validator;
    }

    /**
     * Slugify specified input value.
     *
     * @param array $input
     * @param string $key
     * @param string|null $altKey
     */
    protected function slugifyInput(array &$input, $key, $altKey = null)
    {
        if (! empty($input[$key])) {
            $input[$key] = (new Slugify)->slugify($input[$key]);
        } elseif (! empty($input[$altKey])) {
            $input[$key] = (new Slugify)->slugify($input[$altKey]);
        }
    }
}
