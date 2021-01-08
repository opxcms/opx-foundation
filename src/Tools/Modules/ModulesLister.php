<?php

namespace Core\Tools\Modules;

use Core\Foundation\Application;
use JsonException;
use Symfony\Component\Finder\Finder;

class ModulesLister
{
    /**
     * Get list of enabled modules.
     *
     * @param Application $app
     *
     * @return  array
     */
    public static function getModules(Application $app): array
    {
        $discovered = [];
        $discoveredFileName = $app->storagePath('system' . DIRECTORY_SEPARATOR . 'modules.php');

        if (file_exists($discoveredFileName)) {
            $discovered = require $discoveredFileName;
        }

        $local = self::getRegistrarData($app);

        return array_filter(array_merge($discovered, $local), static function ($module) {
            return $module['enabled'] ?? true;
        });
    }

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
            $reg = require $file;
            if (isset($reg['name'], $reg['module'])) {
                $data[$reg['name']] = [
                    'module' => $reg['module'],
                    'enabled' => $reg['enabled'] ?? true,
                ];
            }
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
     * @throws JsonException
     */
    public static function discoverModules(Application $app): array
    {
        $installedPath = $app->basePath('vendor' . DIRECTORY_SEPARATOR . 'composer' . DIRECTORY_SEPARATOR . 'installed.json');

        if (!file_exists($installedPath)) {
            return [];
        }

        $packages = json_decode(file_get_contents($installedPath), true, 512, JSON_THROW_ON_ERROR);
        $modules = [];

        $discovered = [];
        $discoveredFileName = $app->storagePath('system' . DIRECTORY_SEPARATOR . 'modules.php');

        if (file_exists($discoveredFileName)) {
            $discovered = require $discoveredFileName;
        }

        foreach ($packages as $package) {
            if (isset($package['extra']['opxcms']['module'])) {
                $class = $package['extra']['opxcms']['module'];
                $modules[$package['name']] = [
                    'module' => $class,
                    'enabled' => $discovered[$package['name']]['enabled'] ?? true,
                ];
            }
        }


        file_put_contents($discoveredFileName, '<?php return ' . var_export($modules, true) . ';');

        return $modules;
    }
}