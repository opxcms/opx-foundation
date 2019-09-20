<?php

namespace Core\Foundation\Module;

//use Core\Console\Kernel;
use Core\Foundation\Application;
use Core\Foundation\Module\Traits\CheckMigrations;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class BaseModule is abstract class with implementations to build modules.
 */
abstract class BaseModule extends ServiceProvider
{
    use CheckMigrations;

    /** @var Application  The application instance. Override for IDE. */
    protected $app;

    /** @var array  Configurations for module. */
    protected $config = [];

    /** @var bool  Is configuration loaded already. */
    protected $configLoaded = false;

    /** @var string  Path to module root. */
    protected $path;

    /** @var string|null  Path to module template dir. */
    protected $templatePath;

    /** @var string  Module name used to register in app container. */
    protected $name;

    /** @var string  Full namespace of module */
    protected $namespace;

    /** @var RouteRegistrar  Route registrar. */
    protected $routeRegistrar;

    /** @var array  Global middlewares to register. Store here [$key => $value, ...] */
//    protected $globalMiddleware;

    /**
     * Route middlewares to register. Store here [$key => $value, ...]
     *
     * @var array
     */
    protected $routeMiddleware;

    /**
     * BaseModule constructor.
     *
     * @param Application $app
     *
     * @return  void
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $class = static::class;

        if(!isset($this->name, $this->path)) {
            throw new RuntimeException("Name and path must be set in [{$class}]");
        }

        $this->namespace = substr($class, 0, -strlen(class_basename($class)) - 1);
    }

    /**
     * Register module.
     *
     * @return  void
     */
    public function register(): void
    {
        $this->app->registerModule($this->name(), $this);

        if ($this->app->inManageMode()) {

            // Define path to migrations
            if (!is_dir($migrationPath = $this->templatePath('Migrations'))) {
                $migrationPath = $this->path('Migrations');
            }

            $this->loadMigrationsFrom($migrationPath);
        }

        // Register global middlewares. Take care of name collisions!
//  	    if(is_array($this->globalMiddleware)) {
//  		    $kernel = $this->app->make('\Illuminate\Contracts\Http\Kernel');
//  		    foreach ($this->globalMiddleware as $globalMiddleware) {
//				$kernel->pushMiddleware($globalMiddleware);
//  		    }
//  	    }

        // Register router middlewares. Take care of name collisions!
        if (is_array($this->routeMiddleware)) {
            foreach ($this->routeMiddleware as $key => $value) {
                $this->app->make('router')->aliasMiddleware($key, $value);
            }
        }
    }

    /**
     * Bootstrap module.
     *
     * @return  void
     */
    public function boot(): void
    {
        // Add namespace for translator
        if (!is_dir($langPath = $this->templatePath('Lang'))) {
            $langPath = $this->path('Lang');
        }
        $this->app->make('translator')->addNamespace($this->name(), $langPath);

        // Add namespace for view
        if (!is_dir($viewPath = $this->templatePath('Views'))) {
            $viewPath = $this->path('Views');
        }
        $this->app->make('view')->addNamespace($this->name(), $viewPath);
    }

    /**
     * Get module name.
     *
     * @return  string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get path to module root directory.
     * If parameter is set adds it to returned path.
     *
     * @param null|string $path
     *
     * @return  string
     */
    public function path($path = ''): string
    {
        return $this->path . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get path to module template directory.
     * If parameter is set adds it to returned path.
     *
     * @param null|string $path
     *
     * @return  string|null
     */
    public function templatePath($path = ''): ?string
    {
        $dir = str_replace(['Modules', '\\'], [$this->app->basePath('templates'), '/'], $this->namespace);

        return $dir . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get configuration of module assigned to $key.
     * If parameter is null function returns whole settings array.
     *
     * @param null|string $key
     *
     * @return  array|string|null
     */
    public function config($key = null)
    {
        if (!$this->configLoaded) {
            $this->loadConfig();
        }

        if ($key === null) {
            return $this->config;
        }

        if (!isset($this->config[$key])) {
            return null;
        }

        return $this->config[$key];
    }

    /**
     * Load config for module.
     */
    protected function loadConfig(): void
    {
        $repository = $this->app['config'];

        if ($config = $repository->get("modules.{$this->name}")) {

            $this->config = $config;

        } else if (file_exists("{$this->path}/config.php")) {

            $this->config = require $this->path . DIRECTORY_SEPARATOR . 'config.php';
        }

        $this->configLoaded = true;
    }

    /**
     * Make modules specific view.
     *
     * @param string $view
     *
     * @return  mixed
     */
    public function view($view)
    {
        return View::make($this->name() . '::' . $view);
    }

    /**
     * Returns full name of modules specific view with namespace.
     *
     * @param string $view
     *
     * @return string
     */
    public function viewName($view): string
    {
        return $this->name() . '::' . $view;
    }

    /**
     * Make included view.
     *
     * @param string $componentName
     * @param array $attributes
     *
     * @return  mixed
     */
//	public function component($componentName, $attributes = [])
//	{
//	  return View::make($this->moduleNameSpace().'::components.'.$componentName)->with(array_merge($attributes, ['module' => $this]));
//	}

    /**
     * Returns manage navigation menu structure.
     * Items are stored in module's file 'Backend/backend.php'
     *
     * @param null
     *
     * @return  array
     */
    public function getManageNavigationMenu(): array
    {
        $fileName = $this->path('Manage' . DIRECTORY_SEPARATOR . 'navigation.php');

        if (!file_exists($fileName)) {
            return [];
        }

        return require $fileName;
    }

    /**
     * Register public API routes for module.
     *
     * @param string|null $profile
     *
     * @return void
     */
    public function registerPublicApiRoutes($profile): void
    {
        if (!$registrar = $this->getRouteRegistrar()) {
            return;
        }

        if (method_exists($registrar, 'registerPublicAPIRoutes')) {
            $registrar->registerPublicAPIRoutes($profile);
        }
    }

    /**
     * Get RouteRegistrar for current module.
     *
     * @return RouteRegistrar
     */
    protected function getRouteRegistrar(): ?RouteRegistrar
    {
        // Try to get RouteRegistrar if it not set yet
        if (!$this->routeRegistrar && class_exists($registrar = $this->namespace('RouteRegistrar'))) {
            $this->routeRegistrar = new $registrar($this);
        }

        return $this->routeRegistrar;
    }

    /**
     * Get module full namespace.
     *
     * @param string|null $class
     *
     * @return  string
     */
    public function namespace($class = null): string
    {
        return $this->namespace . ($class ? '\\' . $class : $class);
    }

    /**
     * Register public routes for module.
     *
     * @param string|null $profile
     *
     * @return void
     */
    public function registerPublicRoutes($profile): void
    {
        if (!$registrar = $this->getRouteRegistrar()) {
            return;
        }

        if (method_exists($registrar, 'registerPublicRoutes')) {
            $registrar->registerPublicRoutes($profile);
        }
    }

    /**
     * Run controller method.
     *
     * @param string $controllerName
     * @param string $method
     * @param mixed ...$parameters
     *
     * @return  mixed
     *
     * @throws \Exception
     */
    public function runController($controllerName, $method, ...$parameters)
    {
        $controller = $this->app->make($this->namespace('Controllers\\') . $controllerName);

        if (!method_exists($controller, $method)) {
            throw new RuntimeException("Method [{$method}] not found in {$controllerName}");
        }
        return $controller->{$method}(...$parameters);
    }

    /**
     * Get list of templates.
     *
     * @return  array
     */
    public function getTemplatesList(): array
    {
        if (!$this->templatePath || !is_dir($path = $this->templatePath('Templates'))) {
            $path = $this->path('Templates');
        }

        $files = File::files($path);
        $result = [];

        foreach ($files as $file) {
            $file = str_replace('.php', '', pathinfo($file, PATHINFO_BASENAME));
            $result[] = $file;
        }

        sort($result);

        return $result;
    }

    /**
     * Get name of template file.
     *
     * @param string $name
     *
     * @return  string
     */
    public function getTemplateFileName(string $name): string
    {
        if (!$this->templatePath || !is_dir($path = $this->templatePath('Templates'))) {
            $path = $this->path('Templates');
        }

        $ext = strpos($name, '.php', -4) === false ? '.php' : null;

        return $path . DIRECTORY_SEPARATOR . $name . $ext;
    }

    /**
     * Get list of views.
     *
     * @return  array
     */
    public function getViewsList(): array
    {
        if (!$this->templatePath || !is_dir($path = $this->templatePath('Views'))) {
            $path = $this->path('Views');
        }
        $files = File::files($path);
        $result = [];

        foreach ($files as $file) {
            $file = pathinfo($file, PATHINFO_BASENAME);
            $result[] = $file;
        }

        sort($result);

        return $result;
    }

    /**
     * Clear route registrar to leave memory free.
     *
     * @return  void
     */
    public function afterRouteRegister(): void
    {
        if ($this->routeRegistrar) {
            unset($this->routeRegistrar);
        }
    }
//    /**
//     * Make site map index record for module
//     * In most cases you must override this function to have dynamic site map.
//     *
//     * @author lozovoyv@gmail.com
//     * @version 1.0.1
//     *
//     * @param null
//     * @return array|null
//     */
//	public function makeSiteMapIndex()
//	{
//		return [];
//	}

//    /**
//     * Make site map array of module
//     * In most cases you must override this function to have dynamic site map.
//     *
//     * @author lozovoyv@gmail.com
//     * @version 1.0.1
//     *
//     * @param null
//     * @return array|null
//     */
//	public function makeSiteMap()
//	{
//		return [];
//	}


}
