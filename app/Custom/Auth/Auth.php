<?php

namespace Custom\Auth;

use BadMethodCallException;
use Illuminate\Support\Manager;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Guard as GuardContract;

class Auth extends AuthManager
{
    /**
     * Multiple authentication config.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The array of Guard implementation.
     *
     * @var array
     */
    protected $auth = [];

    /**
     * Call a custom driver creator.
     *
     * @param  string  $driver
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function callCustomCreator($driver)
    {
        $custom = Manager::callCustomCreator($driver);

        if ($custom instanceof GuardContract) {
            return $custom;
        }

        return new Guard($custom, $this->app['session.store']);
    }

    /**
     * Create an instance of the database driver.
     *
     * @return \Illuminate\Auth\Guard
     */
    public function createDatabaseDriver()
    {
        $provider = $this->createDatabaseProvider();

        return new Guard($provider, $this->app['session.store']);
    }

    /**
     * Create an instance of the database user provider.
     *
     * @return \Illuminate\Auth\DatabaseUserProvider
     */
    protected function createDatabaseProvider()
    {
        $connection = $this->app['db']->connection();

        $table = isset($this->config['database']) ? $this->config['database'] : null;

        return new DatabaseUserProvider($connection, $this->app['hash'], $table);
    }

    /**
     * Create an instance of the Eloquent driver.
     *
     * @return \Illuminate\Auth\Guard
     */
    public function createEloquentDriver()
    {
        $provider = $this->createEloquentProvider();

        return new Guard($provider, $this->app['session.store']);
    }

    /**
     * Create an instance of the Eloquent user provider.
     *
     * @return \Illuminate\Auth\EloquentUserProvider
     */
    protected function createEloquentProvider()
    {
        $model = isset($this->config['eloquent']) ? $this->config['eloquent'] : null;

        return new EloquentUserProvider($this->app['hash'], $model);
    }

    /**
     * Dynamically call the multiple authentication guard driver.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $config = $this->app['config']['auth.multi_auth'];

        $driver = $this->getDefaultDriver();

        if (isset($config[$method])) {
            if (empty($this->config) || $this->config[$driver] != $config[$method][$driver]) {
                $this->config = $config[$method];

                $this->drivers = [];
            }

            $driver = $this->driver();

            $driver->setPrefix($method);

            if (! isset($this->auth[$method])) {
                $this->auth[$method] = $driver;
            }

            return $this->auth[$method];
        }

        throw new BadMethodCallException("Method [$method] does not exist.");
    }
}
