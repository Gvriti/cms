<?php

namespace Custom\Support\Facades\Auth;

use Custom\Auth\Auth;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Custom\Auth\Auth
 * @see \Custom\Auth\Guard
 */
class AuthCms extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        $resolveFacadeInstance = parent::getFacadeRoot();

        return $resolveFacadeInstance->cms();
    }

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
