<?php

namespace Core\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;

class RouteChangedListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('route:cache');
    }
}