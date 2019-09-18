<?php

namespace Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null get()
 * @method static Templater getTemplateFromFile($filename)
 * @method static Templater getTemplateFromArray($array)
 *
 * @method static Templater withLocale($locale)
 *
 * @method static Templater forUser($user)
 * @method static Templater ignorePermissions($permissions)
 * @method static Templater skipPermissions($permissions)
 * @method static Templater ignoreAllPermissions()
 * @method static Templater skipAllPermissions()
 */
class Templater extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'opx.templater';
    }
}
