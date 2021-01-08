<?php

namespace Core\Providers\Route;

use Core\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /** @var  Application */
    protected $app;

    /**
     * This namespace is applied to your controller routes.
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var  string
     */
    protected $namespace = '\Core\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return  void
     */
    public function map(): void
    {
        $this->mapManageRoutes();

        $this->mapModulesRoutes();
    }

    /**
     * Define all manage-side routes.
     *
     * @return  void
     */
    protected function mapManageRoutes(): void
    {
        Route::namespace($this->namespace)
            ->group($this->app->path('Routes/manage.php'));
    }

    /**
     * Call modules to register their routes.
     *
     * @return  void
     */
    public function mapModulesRoutes(): void
    {
        $profile = $this->app->has('opx.profile') ? $this->app['opx.profile'] : 'default';

        foreach (array_keys($this->app->getModulesList()) as $name) {
            $module = $this->app->getModule($name);

            if ($module !== null) {
                $module->registerPublicApiRoutes($profile);
                $module->registerPublicRoutes($profile);
                $module->afterRouteRegister();
            }

        }
    }
}
