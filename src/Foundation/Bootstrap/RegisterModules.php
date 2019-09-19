<?php

namespace Core\Foundation\Bootstrap;

use Core\Tools\Modules\ModulesLister;
use Illuminate\Contracts\Foundation\Application;

class RegisterModules
{
    /**
     * Bootstrap the given application.
     *
     * @param \Core\Foundation\Application|\Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        // Get configuration repository
        $config = $app->make("config");

        // Iterate all modules
        foreach (ModulesLister::getModules($app) as $module) {

            $moduleClassName = $module['module'] ?? null;

            // Push module to providers list to register
            if ($moduleClassName && class_exists($moduleClassName)) {
                $config->push('app.providers', $moduleClassName);
            }
        }
    }
}
