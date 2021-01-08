<?php

namespace Core\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return Response|RedirectResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {

            if (!empty(array_intersect(['admin', 'admin_api', 'manager', 'manager_api'], $e->guards()))) {
                return $this->unauthenticatedManage($request, $e);
            }
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an manage authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|JsonResponse|RedirectResponse
     */
    protected function unauthenticatedManage(Request $request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest(route('manage_login'));
    }

    /**
     * Convert an manage api authentication exception into a response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|JsonResponse|RedirectResponse
     */
    protected function unauthenticatedApiManage(Request $request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest(route('manage_login'));
    }
}
