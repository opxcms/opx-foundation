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
        $this->app->bind('opx.templater', static function($app){
           return new Templater();
        });
    }
}
