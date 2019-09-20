<?php

namespace Core\Providers;

use Core\Listeners\RouteChangedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Core\Events\RouteChanged;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RouteChanged::class => [
            RouteChangedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
