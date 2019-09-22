<?php

namespace Core\Http\Controllers;

use Core\Traits\NotAuthorizedResponse;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Exception;
use Modules\Admin\Authorization\AdminAuthorization;

class ApiActionsController extends Controller
{
    use NotAuthorizedResponse;

    /**
     * Delete models with given ids.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string|null $permission
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    protected function deleteModels(string $modelClass, array $ids, ?string $permission): JsonResponse
    {
        if($permission !== null && !AdminAuthorization::can($permission)) {
            return $this->returnNotAuthorizedResponse();
        }

        $models = $this->getModels($modelClass, $ids);

        if ($models->count() > 0) {
            /** @var Model $model */
            foreach ($models as $model) {
                $model->delete();
            }
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Restore models with given ids.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string|null $permission
     *
     * @return JsonResponse
     */
    protected function restoreModels(string $modelClass, array $ids, ?string $permission): JsonResponse
    {
        if($permission !== null && !AdminAuthorization::can($permission)) {
            return $this->returnNotAuthorizedResponse();
        }

        $models = $this->getModels($modelClass, $ids, true, true);

        if ($models->count() > 0) {
            /** @var Model $model */
            foreach ($models as $model) {
                $model->restore();
            }
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Set models with given ids as enabled.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string $field
     * @param string|null $permission
     *
     * @return  JsonResponse
     */
    protected function enableModels(string $modelClass, array $ids, string $field, ?string $permission): JsonResponse
    {
        if($permission !== null && !AdminAuthorization::can($permission)) {
            return $this->returnNotAuthorizedResponse();
        }

        $models = $this->getModels($modelClass, $ids, true);

        if ($models->count() > 0) {
            /** @var Model $model */
            foreach ($models as $model) {
                if ((bool)$model->getAttribute($field) === false) {
                    $model->setAttribute($field, true);
                    $model->save();
                }
            }
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Set models with given ids as disabled.
     *
     * @param string $modelClass
     * @param array $ids
     * @param string $field
     * @param string|null $permission
     *
     * @return  JsonResponse
     */
    protected function disableModels(string $modelClass, array $ids, string $field, ?string $permission): JsonResponse
    {
        if($permission !== null && !AdminAuthorization::can($permission)) {
            return $this->returnNotAuthorizedResponse();
        }

        $models = $this->getModels($modelClass, $ids, true);

        if ($models->count() > 0) {
            /** @var Model $model */
            foreach ($models as $model) {
                if ((bool)$model->getAttribute($field) === true) {
                    $model->setAttribute($field, false);
                    $model->save();
                }
            }
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Get models list.
     *
     * @param string $modelClass
     * @param array $ids
     * @param bool $withTrashed
     * @param bool $onlyTrashed
     *
     * @return  Collection
     */
    protected function getModels(string $modelClass, array $ids, bool $withTrashed = false, bool $onlyTrashed = false): Collection
    {
        /** @var EloquentBuilder $query */
        $query = call_user_func([$modelClass, 'query']);

        $query->whereIn('id', $ids);

        if ($onlyTrashed) {
            $query->onlyTrashed();
        } elseif ($withTrashed) {
            $query->withTrashed();
        }

        return $query->get();
    }
}