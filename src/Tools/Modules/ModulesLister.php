<?php

namespace Core\Tools\Modules;

use Core\Foundation\Application;
use Symfony\Component\Finder\Finder;

class ModulesLister
{
    /**
     * Collect registration data from all modules.
     *
     * @param  \Core\Foundation\Application  $app
     * @param  int  $depth
     * @param  string  $registerFileName
     *
     * @return  array
     */
    public static function getRegistrarData(Application $app, $depth = 3, $registerFileName = 'register.php')
    {
        $data = [];

        foreach (self::getRegistrarFiles($app, $depth, $registerFileName) as $file) {
            $data[] = [
                'path' => $file->getPath(),
                'data' =>require $file,
            ];
        }

        return $data;
    }

    /**
     * @param  \Core\Foundation\Application  $app
     * @param  int $depth
     * @param  string  $registerFileName
     *
     * @return  \Symfony\Component\Finder\Finder
     */
    public static function getRegistrarFiles(Application $app, $depth = 3, $registerFileName = 'register.php')
    {

        return Finder::create()->depth('< '.($depth + 1))->name($registerFileName)->in($app->getModulesPath());
    }
}