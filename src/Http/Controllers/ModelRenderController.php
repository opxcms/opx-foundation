<?php

namespace Core\Http\Controllers;

use Core\Facades\Site;
use Core\Foundation\Module\BaseModule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RuntimeException;

class ModelRenderController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var string */
    protected $route;

    /** @var BaseModule */
    protected $module;

    /** @var Model */
    protected $model;

    /** @var Carbon */
    protected $lastModifiedTime;

    /** @var array|null */
    protected $models;

    /** @var string|null */
    protected $layout;

    /**
     * RenderController constructor.
     *
     * @return  void
     */
    public function prepare(): void
    {
        $this->route = Route::currentRouteName();

        $this->extractParameters();

        // check if model exists
        if (!isset($this->model)) {
            throw new RuntimeException("Can not retrieve model for route [{$this->route}]");
        }

        // check if model is published
        if (method_exists($this->model, 'isPublished') && $this->model->isPublished() === false) {
            abort(404);
        }
    }

    /**
     * Run render queue.
     *
     * @return Response
     */
    public function renderModel(): Response
    {
        $this->prepare();

        // check if 304 response is applicable
        if ($this->needsNotModifiedSinceResponse()) {
            // send not modified response with null content
            return $this->sendNotModifiedResponse();
        }

        // handle cache
        $content = $this->getContentUsingCache();

        // send response
        return $this->sendResponseWithLastModified($content);
    }

    /**
     * Get parameters from current route.
     *
     * @return  void
     * @throws BindingResolutionException
     */
    protected function extractParameters(): void
    {
        $parameters = explode('::', $this->route);

        if (!isset($parameters[0])) {
            throw new RuntimeException("Module name assigned to route [{$this->route}] is empty.");
        }

        $this->module = app()->make($parameters[0]);

        if (isset($parameters[1])) {
            $modelClass = $this->models[$parameters[1]]
                ?? $this->module->namespace('Models\\' . str_replace('_', '', Str::title($parameters[1])));
        }

        $id = $parameters[2] ?? null;

        if ($id !== null && isset($modelClass)) {
            $this->model = call_user_func([$modelClass, 'find'], $id);
        }
    }

    /**
     * Extract last modified time for model.
     *
     * @return  void
     */
    protected function extractLastModifiedTime(): void
    {
        if (!$this->model || isset($this->lastModifiedTime)) {
            return;
        }

        $time = $this->model->getAttribute('updated_at');

        // is model has last updated time
        if ($time === null) {
            return;
        }

        $this->lastModifiedTime = $time instanceof Carbon ? $time : Carbon::parse($time);
    }

    /**
     * Check if NotModifiedSince response is needed.
     *
     * @return  bool
     */
    protected function needsNotModifiedSinceResponse(): bool
    {
        // is response enabled at all
        if (!$this->model->getAttribute('site_map_last_mod_enable')
            || !config('site.304_response')
            || !request()->hasHeader('If-Modified-Since')
        ) {
            return false;
        }

        $this->extractLastModifiedTime();

        // is model has last updated time
        if ($this->lastModifiedTime === null) {
            return false;
        }

        $requested = Carbon::createFromFormat('D\, d M Y H:i:s \G\M\T', request()->header('If-Modified-Since'), 'GMT')->timestamp;
        $actual = $this->lastModifiedTime->setTimezone('GMT')->timestamp;

        return $requested >= $actual;
    }

    /**
     * Send NotModifiedSince response.
     *
     * @return Response
     */
    protected function sendNotModifiedResponse(): Response
    {
        return response(null, 304);
    }

    /**
     * Send response with content and last modified time.
     *
     * @param $content
     *
     * @return Response
     */
    protected function sendResponseWithLastModified($content): Response
    {
        $updateTime = $this->lastModifiedTime ?? Carbon::now();

        return response($content, 200, ['Last-Modified' => $updateTime->format('D, d M Y H:i:s') . ' GMT']);
    }

    /**
     * Search and run layout specific render function.
     *
     * @return  mixed
     */
    protected function renderLayout()
    {
        $layoutFile = $this->model->getAttribute('layout') ?? $this->layout;

        if ($layoutFile === null) {
            $class = get_class($this->model);
            $id = $this->model->getAttribute('id');

            throw new RuntimeException("Layout for [{$class}] with id [{$id}] not set.");
        }

        $layoutName = str_replace('.blade.php', '', $layoutFile);
        $method = 'render' . str_replace('_', '', Str::title($layoutName)) . 'Layout';

        if (method_exists($this, $method)) {
            return $this->$method($layoutName);
        }

        return $this->renderDefaultLayout($layoutName);
    }

    /**
     * Render default layout for model.
     *
     * @param string $layout
     *
     * @return  mixed
     */
    protected function renderDefaultLayout(string $layout)
    {
        $request = request();

        $styles = Site::getAssetStyles();
        Site::setAssetStyles();
        Site::addInjectStyle(array_merge(...$styles));

        Site::setMetaTitle($this->model->getAttribute('meta_title'));
        Site::setMetaDescription($this->model->getAttribute('meta_description'));
        Site::setMetaIndex(empty($this->model->getAttribute('no_index')));
        Site::setMetaFollow(empty($this->model->getAttribute('no_follow')));
        if ($request->has('page')) {
            $page = $request->input('page');
            $canonical = $this->model->getAttribute('canonical');
            if ($page !== null) {
                Site::setMetaCanonical($canonical ?? $request->path());
            }
        }

        return $this->module->view($layout)->with(['model' => $this->model]);
    }

    /**
     * Handle cache for current model layout.
     *
     * @return mixed|null
     */
    protected function getContentUsingCache()
    {
        if ($this->lastModifiedTime === null || config('site.cache_enable') !== true) {
            return $this->renderLayout();
        }

        $key = $this->route;
        $cachedTimestamp = Cache::get($key . '_time');
        $lastModifiedTime = $this->lastModifiedTime->toIso8601String();

        // try to return cached content
        if (!empty($cachedTimestamp) && ($cachedTimestamp === $lastModifiedTime)) {
            $content = Cache::get($key);
            if (!empty($content)) {

                return $content;
            }
        }

        // if no cached content or cache expired
        // make new cache
        $content = $this->renderLayout();

        Cache::forever($key . '_time', $lastModifiedTime);
        Cache::forever($key, $content);

        return $content;
    }
}
