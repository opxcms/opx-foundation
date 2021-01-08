<?php

namespace Core\Providers;

use Core\Foundation\Templater\Templater;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('opx.templater', static function($app){
           return new Templater();
        });
    }
}
