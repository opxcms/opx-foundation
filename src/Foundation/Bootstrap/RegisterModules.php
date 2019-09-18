<?php

namespace Core\Foundation\Bootstrap;

use Core\Tools\Modules\ModulesLister;
use Illuminate\Contracts\Foundation\Application;

class RegisterModules
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Core\Foundation\Application|\Illuminate\Contracts\Foundation\Application $app
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
        foreach (ModulesLister::getRegistrarData($app) as $moduleRegistration) {

            $moduleClassName = $moduleRegistration['data']['provider'] ?? null;
            $moduleFacade = $moduleRegistration['data']['facade'] ?? null;
            $moduleFacadeAlias = $moduleRegistration['data']['facade_alias'] ?? null;
            $modulePath = $moduleRegistration['path'] ?? null;

            $modelsClasses = $moduleRegistration['data']['models'] ?? null;
            if (null !== $modelsClasses) {
                foreach ($modelsClasses as $modelsClass) {
                    $config->push('modules.models', $modelsClass);
                }
            }

            $pluginsClasses = $moduleRegistration['data']['extends_models'] ?? null;
            if (null !== $pluginsClasses) {
                foreach ($pluginsClasses as $pluginsClass => $extendsClasses) {
                    foreach ($extendsClasses as $extendsClass => $priority) {
                        $config->push("modules.plugins.{$extendsClass}.{$priority}", $pluginsClass);
                    }
                }
            }

            // Push module to providers list to register and add facade alias
            if ($moduleClassName && class_exists($moduleClassName)) {
                $config->set("modules.paths.{$moduleClassName}", $modulePath);
                $config->push('app.providers', $moduleClassName);
                if ($moduleFacadeAlias && $moduleFacade && class_exists($moduleFacade)) {
                    $config->set('app.aliases.' . $moduleFacadeAlias, $moduleFacade);
                }
            }
        }
    }
}
