<?php

namespace Core\Providers;

use Core\Foundation\SocketBroadcaster\SocketBroadcaster;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Broadcasting\BroadcastManager;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->make(BroadcastManager::class)->extend(
            'websocket',
            function ($app, $config) {
                return new SocketBroadcaster($app, $config);
            }
        );

        Broadcast::routes();
    }
}
