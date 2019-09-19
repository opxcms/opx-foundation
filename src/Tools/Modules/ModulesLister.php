<?php

namespace Core\Tools\Modules;

use Core\Foundation\Application;
use Symfony\Component\Finder\Finder;

class ModulesLister
{
    /**
     * Collect registration data from all modules.
     *
     * @param Application $app
     * @param int $depth
     *
     * @param string $registerFileName
     *
     * @return  array
     */
    public static function getRegistrarData(Application $app, $depth = 3, $registerFileName = 'register.php'): array
    {
        $data = [];

        // Get local modules from `modules` folder.
        foreach (self::getRegistrarFiles($app, $depth, $registerFileName) as $file) {
            $data[] = [
                'path' => $file->getPath(),
                'data' => require $file,
            ];
        }

        return $data;
    }

    /**
     * @param Application $app
     * @param int $depth
     * @param string $registerFileName
     *
     * @return  Finder
     */
    public static function getRegistrarFiles(Application $app, $depth = 3, $registerFileName = 'register.php'): Finder
    {
        return Finder::create()->depth('< ' . ($depth + 1))->name($registerFileName)->in($app->getModulesPath());
    }

    /**
     * Discover modules.
     *
     * @param Application $app
     *
     * @return  array
     */
    public static function discoverModules(Application $app): array
    {
        $installedPath = $app->basePath('composer/installed.json');

        if (!file_exists($installedPath)) {
            return;
        }

        $packages = json_decode(require $installedPath, true);

        $modules = [];

        $discovered = [];
        $discoveredFileName = $app->storagePath('system/modules.php');

        if (file_exists($discoveredFileName)) {
            $discovered = require $discoveredFileName;
        }

        foreach ($packages as $package) {
            if (isset($packag['extra']['opxcms']['module'])) {
                $class = $package['extra']['opxcms']['module'];
                $modules[$package['name']] = [
                    'module' => $class,
                    'path' => $class,
                    'enabled' => $discovered[$package['name']] ?? true,
                ];
            }
        }

        file_put_contents($discoveredFileName, '<?php return ' . var_export($modules, true) . ';');

        return $modules;
    }
}