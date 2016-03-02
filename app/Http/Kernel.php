<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'CmsAuth' => \App\Http\Middleware\Admin\AdminAuthenticate::class,
        'CmsGuest' => \App\Http\Middleware\Admin\AdminRedirectIfAuthenticated::class,
        'CmsLockscreen' => \App\Http\Middleware\Admin\AdminLockscreen::class,

        'SiteAuth' => \App\Http\Middleware\Site\SiteAuthenticate::class,
        'SiteGuest' => \App\Http\Middleware\Site\SiteRedirectIfAuthenticated::class,

        // 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    ];
}
