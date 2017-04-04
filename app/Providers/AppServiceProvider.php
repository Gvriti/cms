<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app['config']->get('app.debug')) {
            log_executed_db_queries();
        }

        $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler()
            ->directive('php', function ($expression) {
                return "<?php {$expression}; ?>";
            });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
