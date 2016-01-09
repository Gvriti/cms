<?php

namespace Custom\Auth;

use Illuminate\Auth\Guard as BaseGuard;

class Guard extends BaseGuard
{
    /**
     * The session prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new multiple authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface  $session
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $prefix
     * @return void
     */
    public function __construct($provider, $session, $request = null, $prefix = null)
    {
        parent::__construct($provider, $session, $request);

        $this->prefix = $prefix;
    }

    /**
     * Get the currently authenticated user.
     *
     * @param  string|null  $attribute
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function get($attribute = null)
    {
        if (is_null($attribute)) {
            return $this->user();
        }

        return $this->user()->$attribute;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        parent::clearUserDataFromStorage();
    }

    /**
     * Get the session prefix.
     *
     * @param  string  $prefix
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the session prefix.
     *
     * @param  string  $prefix
     * @return string
     */
    public function setPrefix($prefix)
    {
        return $this->prefix = $prefix;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->prefix . '_' . parent::getName();
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return  $this->prefix . '_' . parent::getRecallerName();
    }

    /**
     * Dynamically retrieve attributes on the currently authenticated user.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get()->$key;
    }
}
