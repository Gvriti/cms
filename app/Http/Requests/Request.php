<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Request extends FormRequest
{
    /**
     * The array of custom errors.
     *
     * @var array
     */
    protected $customErrors = [];

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        parent::validate();

        if (! empty($this->customErrors)) {
            throw new HttpResponseException($this->response([]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function response(array $errors)
    {
        foreach ((array) $this->customErrors as $key => $value) {
            if (is_array($value)) {
                $errors[$key] = array_merge($errors[$key], $value);
            } else {
                $errors[$key][] = $value;
            }
        }

        return parent::response($errors);
    }
}
