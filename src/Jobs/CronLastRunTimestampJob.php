<?php

namespace Core\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;

/**
 * Class CronLastRunTimestamp
 * @package Core\Jobs
 */
class CronLastRunTimestampJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return  void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timestamp = Carbon::now('UTC');

        $file = app()->storagePath().DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'cron_timestamp';

        File::put($file, "{$timestamp} UTC");
    }
}
