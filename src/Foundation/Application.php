<?php

namespace Core\Foundation;

use Closure;
use Core\Foundation\Module\BaseModule;
use RuntimeException;
use Illuminate\Foundation\Events\LocaleUpdated;

class Application extends \Illuminate\Foundation\Application
{
    /**
     * The Laravel framework version.
     *
     * @var string
     */
    protected const OPX_VERSION = '0.7.1';

    /** @var  string  The environment file to load during bootstrapping. */
    protected $environmentFile = '.env';

    /** @var  array  Scheduled  jobs. */
    protected $schedulesJobs;

    /** @var  array  Profiling store. */
    protected $profiling;

    /** @var  array  Registered modules. */
    protected $modules = [];

    /** @var  boolean  Flag to disable some functions needed only when managing application. */
    protected $manageMode = false;

    /** @var string  Real path of core. */
    protected $corePath;

    public function __construct($basePath = null)
    {
        $this->corePath = dirname(__DIR__);

        parent::__construct($basePath);
    }

    /**
     * Get the path to the application "core" directory.
     *
     * @param string $path Optionally, a path to append to the app path
     *
     * @return string
     */
    public function path($path = ''): string
    {
        return $this->corePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param string $path Optionally, a path to append to the base path
     *
     * @return string
     */
    public function basePath($path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the bootstrap directory.
     *
     * @param string $path Optionally, a path to append to the bootstrap path
     *
     * @return string
     */
    public function bootstrapPath($path = ''): string
    {
        return $this->basePath($path);
    }

    /**
     * Get the path to the main application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     *
     * @return string
     */
    public function configPath($path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the local application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     *
     * @return string
     */
    public function localConfigPath($path = ''): string
    {
        return $this->storagePath('config') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the storage directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath($path = ''): string
    {
        return $this->storagePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the database directory.
     *
     * @param string $path Optionally, a path to append to the database path
     *
     * @return string
     */
    public function databasePath($path = ''): string
    {
        return ($this->databasePath ?: $this->corePath . DIRECTORY_SEPARATOR . 'Database') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public function langPath(): string
    {
        return $this->corePath . DIRECTORY_SEPARATOR . 'Lang';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function publicPath(): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public_html';
    }

    /**
     * Get the path to the cached services.php file.
     *
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        return $this->storagePath() . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'services.php';
    }

    /**
     * Get the path to the cached packages.php file.
     *
     * @return string
     */
    public function getCachedPackagesPath(): string
    {
        return $this->storagePath() . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'packages.php';
    }

    /**
     * Get the path to the configuration cache file.
     *
     * @return string
     */
    public function getCachedConfigPath(): string
    {
        return $this->storagePath() . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'config.php';
    }

    /**
     * Get the path to the routes cache file.
     *
     * @return string
     */
    public function getCachedRoutesPath(): string
    {
        return $this->storagePath() . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'routes.php';
    }

    /**
     * Get the path to the users repositories files.
     *
     * @param string $path
     *
     * @return string
     */
    public function getUsersPath($path = ''): string
    {
        return $this->storagePath() . DIRECTORY_SEPARATOR . 'users' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance(): bool
    {
        return file_exists($this->storagePath() . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'down');
    }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getNamespace(): string
    {
        if ($this->namespace === null) {
            $class = static::class;
            $this->namespace = substr($class, 0, -strlen(class_basename($class)) - 1);
            $this->namespace = substr($this->namespace, 0, -strlen(class_basename($this->namespace)) - 1);
        }

        return $this->namespace;
    }

    /**
     * Get the path to the modules folder.
     *
     * @param string $path Optionally, a path to append to the modules path
     *
     * @return string
     */
    public function getModulesPath($path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'modules' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the modules folder.
     *
     * @param string $path Optionally, a path to append to the modules path
     *
     * @return string
     */
    public function getAssetsPath($path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'assets' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this['config']->get('lang.locale');
    }

    /**
     * Set the current application locale.
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale): void
    {
        $this['config']->set('lang.locale', $locale);

        $this['translator']->setLocale($locale);

        $this['events']->dispatch(new LocaleUpdated($locale));
    }

    /**
     * Get the current application fallback locale.
     *
     * @return string
     */
    public function getFallbackLocale(): string
    {
        return $this['config']->get('lang.fallback_locale');
    }

    /**
     * Register job scheduling closure.
     *
     * @param Closure $closure
     *
     * @return  void
     */
    public function registerScheduledJob(Closure $closure): void
    {
        $this->schedulesJobs[] = $closure;
    }

    /**
     * Get scheduled jobs list.
     *
     * @return  array
     */
    public function getScheduledJobs(): array
    {
        return $this->schedulesJobs ?? [];
    }

    /**
     * Get the version number of the OPX.
     *
     * @return string
     */
    public function opxVersion(): string
    {
        return static::OPX_VERSION;
    }

    /**
     * Push way point to profiler.
     *
     * @param string $wayPoint
     *
     * @return  void
     */
    public function pushToProfiler($wayPoint): void
    {
        $this->profiling[] = [
            'time' => microtime(true) - LARAVEL_START,
            'wayPoint' => $wayPoint,
        ];
    }

    /**
     * Get profiler records.
     *
     * @return  array
     */
    public function getProfiler(): array
    {
        return $this->profiling;
    }

    /**
     * Bind module to application.
     *
     * @param string $name
     * @param mixed $module
     *
     * @return  void
     */
    public function registerModule($name, $module): void
    {
        $this->modules[$name] = get_class($module);
        $this->instance($name, $module);
    }

    /**
     * Get modules list registered in application.
     *
     * @return  array
     */
    public function getModulesList(): array
    {
        return $this->modules ?? [];
    }

    /**
     * Resolve module
     *
     * @param string $name
     *
     * @return  null|BaseModule
     */
    public function getModule($name): ?BaseModule
    {
        if (!isset($this->modules[$name])) {
            return null;
        }

        $module = $this->getProvider($this->modules[$name]);

        return $module instanceof BaseModule ? $module : null;
    }

    /**
     * Check if application in managing mode.
     *
     * @return  boolean
     */
    public function inManageMode(): bool
    {
        return $this->manageMode || $this->runningInConsole();
    }
}
