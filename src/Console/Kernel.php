<?php

namespace Core\Console;

use Core\Foundation\Application;
use Core\Jobs\CronLastRunTimestampJob;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use ReflectionException;

class Kernel extends ConsoleKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Core\Foundation\Bootstrap\LoadRawConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Core\Foundation\Bootstrap\RegisterModules::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Make timestamp to monitor cron is working
        $schedule->job(new CronLastRunTimestampJob())->everyMinute();

        // Run all scheduled jobs
        foreach (app()->getScheduledJobs() as $scheduledJob) {
            $scheduledJob($schedule);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return  void
     *
     * @throws ReflectionException
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        $paths = [];

        /** @var Application $app */
        $app = $this->app;
        $modules = $app->getModulesList();
        foreach (array_keys($modules) as $module) {
            if (($instance = $app->getModule($module)) && is_dir($dir = $instance->path('Console'))) {
                $paths[] = $dir;
            }
        }

        if (empty($paths)) {
            return;
        }

        foreach ((new Finder)->in($paths)->files() as $command) {
            $name = 'Modules\\' . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($command->getPathname(), $app->basePath('modules') . DIRECTORY_SEPARATOR)
                );

            if (is_subclass_of($name, Command::class) &&
                !(new ReflectionClass($name))->isAbstract()) {
                Artisan::starting(function ($artisan) use ($name) {
                    $artisan->resolve($name);
                });
            }
        }
    }
}
