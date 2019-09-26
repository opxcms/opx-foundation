<?php

namespace Core\Http;

use Core\Foundation\Bootstrap\LoadConfiguration;
use Core\Foundation\Bootstrap\RegisterModules;
use Core\Foundation\JWT\Middleware\CreateFreshApiToken;
use Core\Foundation\JWT\Middleware\EncryptCookies as EncryptJWTCookies;
use Core\Http\Middleware\EncryptCookies;
use Core\Http\Middleware\HTMLMinify;
use Core\Http\Middleware\RedirectIfAuthenticated;
use Core\Http\Middleware\RedirectIfManageAuthenticated;
use Core\Http\Middleware\RedirectIndex;
use Core\Http\Middleware\SwitchLocale;
use Core\Http\Middleware\TrimStrings;
use Core\Http\Middleware\TrustProxies;
use Core\Http\Middleware\VerifyCsrfToken;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
//use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        HandleExceptions::class,
        RegisterModules::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        RedirectIndex::class,
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            // 'set.locale:manage',
            HTMLMinify::class,
        ],

        'web_api' => [
            EncryptJWTCookies::class,
            StartSession::class,
            // AuthenticateSession::class,
            'throttle:60,1',
            SubstituteBindings::class,
        ],

        'manage' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            CreateFreshApiToken::class,
            'set.locale:manage',
        ],

        'manage_api' => [
            EncryptJWTCookies::class,
            StartSession::class,
            // AuthenticateSession::class,
            'throttle:60,1',
            SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // Auth related
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'can' => Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'manage.not.authenticated' => RedirectIfManageAuthenticated::class,

        'bindings' => SubstituteBindings::class,
        'cache.headers' => SetCacheHeaders::class,
        'signed' => ValidateSignature::class,
        'throttle' => ThrottleRequests::class,
        'set.locale' => SwitchLocale::class,
    ];

    /**
     * Add custom middleware to application's route middleware.
     *
     * @param array $routeMiddleware
     *
     * @return void
     */
    public function addRouteMiddleware($routeMiddleware): void
    {
        $this->routeMiddleware = array_merge($this->routeMiddleware, $routeMiddleware);
    }
}
