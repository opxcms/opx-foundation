<?php

namespace Core\Http\Controllers\Api;

use Core\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\Admin\Navigation\AdminNavigation;

class ManageNavigationApiController extends Controller
{
    /**
     * Get navigation list from system.
     *
     * @return  JsonResponse
     *
     * @throws  Exception
     */
    public function getNavigation(): JsonResponse
    {
        $list = AdminNavigation::getNavigation();

        return response()->json($list);
    }

    /**
     * Set navigation favorites.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  Exception
     */
    public function postFavorites(Request $request): JsonResponse
    {
        AdminNavigation::storeFavorites($request->all());

        return response()->json(['message' => 'Favorites stored.']);
    }
}
