<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param  \Illuminate\View\Compilers\BladeCompiler $bladeCompiler
     * @return void
     */
    public function boot(BladeCompiler $bladeCompiler)
    {
        if ($this->app['config']->get('app.debug')) {
            log_executed_db_queries();
        }

        $bladeCompiler->directive('php', function ($expression) {
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
