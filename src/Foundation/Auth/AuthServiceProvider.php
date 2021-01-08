<?php

namespace Core\Foundation\Auth;

use Core\Foundation\UserSettings\UserSettingsRepository;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register file based UserProvider
        $this->app['auth']->provider('file', static function ($app, array $config) {
            if(isset($config['settings']['provider'])) {
                app()->bind(UserSettingsRepository::class, $config['settings']['provider']);
                app()->instance('user.settings.repository', $config['settings']['repository'] ?? null);
            }
            return new UserProvider(
                $config,
                new UserFileRepository(
                    $app,
                    $config['repository'],
                    new RememberTokenFileRepository($app, $config['repository']),
                    $app['hash'],
                    $app['cache']
                )
            );
        });
    }
}
