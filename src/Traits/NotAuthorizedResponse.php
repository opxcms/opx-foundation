<?php

namespace Core\Traits;

use Illuminate\Http\JsonResponse;

trait NotAuthorizedResponse
{
    /**
     * Return NotAuthorizedResponse
     *
     * @return  JsonResponse
     */
    protected function returnNotAuthorizedResponse(): JsonResponse
    {
        return response()->json(['message' => trans('manage.not_authorized')], 519);
    }
}