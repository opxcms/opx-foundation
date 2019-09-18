<?php

namespace Core\Foundation\JWT;

use Illuminate\Auth\RequestGuard;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Core\Foundation\Guards\TokenGuard;
use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->deleteCookieOnLogout();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGuard();
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard()
    {
        Auth::extend('jwt', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return RequestGuard
     */
    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard(
//                $this->app->make(ResourceServer::class),
                Auth::createUserProvider($config['provider']),
//                new TokenRepository,
//                $this->app->make(ClientRepository::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }

    /**
     * Register the cookie deletion event handler.
     *
     * @return void
     */
    protected function deleteCookieOnLogout()
    {
        Event::listen(Logout::class, function () {
            if (Request::hasCookie(JWT::cookie())) {
                Cookie::queue(Cookie::forget(JWT::cookie()));
            }
        });
    }
}
