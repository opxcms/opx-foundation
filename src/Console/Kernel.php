<?php

namespace Core\Console;

use Core\Console\Commands\DiscoverModulesCommand;
use Core\Foundation\Application;
use Core\Foundation\Bootstrap\LoadRawConfiguration;
use Core\Foundation\Bootstrap\RegisterModules;
use Core\Jobs\CronLastRunTimestampJob;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Bootstrap\SetRequestForConsole;
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
        LoadEnvironmentVariables::class,
        LoadRawConfiguration::class,
        HandleExceptions::class,
        RegisterModules::class,
        RegisterFacades::class,
        SetRequestForConsole::class,
        RegisterProviders::class,
        BootProviders::class,
    ];
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DiscoverModulesCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return  void
     */
    protected function schedule(Schedule $schedule): void
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
                Artisan::starting(static function ($artisan) use ($name) {
                    $artisan->resolve($name);
                });
            }
        }
    }
}
