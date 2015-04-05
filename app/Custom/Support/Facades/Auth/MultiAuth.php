<?php

namespace Custom\Support\Facades\Auth;

use Custom\Auth\Auth;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Custom\Auth\Auth
 * @see \Custom\Auth\Guard
 */
class MultiAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Auth::class;
    }
}
