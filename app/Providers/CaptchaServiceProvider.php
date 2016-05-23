<?php

namespace App\Providers;

use Mews\Captcha\CaptchaServiceProvider as Captcha;

class CaptchaServiceProvider extends Captcha
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->group(['middleware' => 'web'], function() {
            parent::boot();
        });
    }
}
