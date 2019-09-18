<?php

namespace Core\Foundation\Module;

class RouteRegistrar
{
    /** @var  BaseModule  Module instance. */
    protected $module;

    /**
     * RouteRegistrar constructor.
     *
     * @param BaseModule $module
     */
    public function __construct(BaseModule $module)
    {
        $this->module = $module;
    }

    /**
     * Register public routes.
     *
     * @param string $profile
     *
     * @return  void
     */
    public function registerPublicRoutes($profile): void
    {

    }

    /**
     * Register API routes.
     *
     * @param string $profile
     *
     * @return  void
     */
    public function registerPublicAPIRoutes($profile)
    {

    }
}