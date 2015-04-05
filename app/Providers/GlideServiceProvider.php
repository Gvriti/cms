<?php

namespace App\Providers;

use League\Glide\Server;
use League\Glide\ServerFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\Filesystem;

class GlideServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Server::class, function ($app) {
            $filesystem = $app[Filesystem::class];

            $source = public_path(current((array) $app['config']->get('elfinder.dir')));

            return (new ServerFactory([
                'source'             => $source,
                'cache'              => $filesystem->getDriver(),
                'cache_path_prefix'  => 'images/cache',
            ]))->getServer();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Server::class];
    }
}
