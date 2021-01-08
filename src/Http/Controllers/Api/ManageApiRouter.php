<?php

namespace Core\Http\Controllers\Api;

use Core\Foundation\Module\BaseModule;
use Core\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ManageApiRouter extends Controller
{
    /**
     * Handle all incoming api request and dispatch it.
     *
     * @param Request $request
     *
     * @return  ResponseFactory|Response
     */
    public function handleRequest(Request $request)
    {
        try {
            // Extract segments from request
            $segments = $this->segments($request);

            // Get method of request
            $method = strtolower($request->method());

            // if $segments[0] is 'module' we need to forward request to module ApiController
            if ($segments[0] === 'module') {

                unset($segments[0]);

                $result = $this->forwardRequest($request, array_values($segments), $method);

                // else we need to find controller and run method
            } else {

                $controllerName = 'Core\Http\Controllers\Api\Manage' . str_replace('_', '', Str::title($segments[0])) . 'ApiController';

                $controller = app()->make($controllerName);

                $methodName = $method . str_replace('_', '', Str::title($segments[1]));

                unset($segments[1], $segments[0]);

                $result = $controller->$methodName($request, array_values($segments));

            }
        } catch (Exception $e) {
            $result = response()
                ->json(['message' => $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile()], 500);
        }

        return $result;
    }

    /**
     * Get all of the segments for the request API path.
     *
     * @param Request $request
     *
     * @return  array
     */
    protected function segments(Request $request): array
    {
        $basePath = route(Route::currentRouteName(), [], false);
        $fullPath = '/' . $request->decodedPath();

        $path = substr($fullPath, strpos($fullPath, $basePath) + strlen($basePath) + 1);

        $segments = explode('/', $path);

        return array_values(array_filter($segments, static function ($value) {
            return $value !== '';
        }));
    }

    /**
     * Forward request to module.
     *
     * @param Request $request
     * @param array $segments
     * @param string $method
     *
     * @return  mixed
     *
     * @throws Exception
     */
    public function forwardRequest(Request $request, array $segments, string $method)
    {
        /** @var BaseModule $module */
        $module = app()->make($segments[0]);

        $controllerName = 'Manage' . str_replace('_', '', Str::title($segments[1])) . 'ApiController';

        if (isset($segments[2])) {
            $functionName = str_replace('_', '', Str::title($segments[2]));
            unset($segments[2]);
        } else {
            $functionName = 'Index';
        }

        unset($segments[1], $segments[0]);

        return $module->runController($controllerName, $method . $functionName, $request, array_values($segments));
    }
}
