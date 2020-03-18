<?php

namespace Core\Http\Controllers\Assets;

use Core\Foundation\Module\BaseModule;
use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    /** @var array MIME types */
    protected $mimeTypes = [
        'ttf' => 'application/x-font-ttf',
        'otf' => 'application/x-font-opentype',
        'woff' => 'application/font-woff',
        'woff2' => 'application/font-woff2',
        'eot' => 'application/vnd.ms-fontobject',
        'sfnt' => 'application/font-sfnt',
        'css' => 'text/css',
        'svg' => 'image/svg+xml',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'icon' => 'image/vnd.microsoft.icon',
        'js' => 'application/javascript',
    ];

    protected $noCacheExtensions = [
        'css',
        'js',
    ];

    /** @var string Default MIME type */
    protected $defaultMimeType = 'application/octet-stream';

    /** @var  integer  Cache lifetime in seconds. */
    protected $cacheLifeTime = 604800;

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getSystemAsset(string $asset)
    {
        $path = app()->getAssetsPath('manage/assets/system');

        return $this->getAsset($path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getPublicAsset(string $asset)
    {
        $path = app()->getAssetsPath('manage/assets/public');

        return $this->getAsset($path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $module
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getModuleSystemAsset(string $module, string $asset)
    {
        /** @var BaseModule $moduleInstance */
        $moduleInstance = app()->getModule($module);

        if (!$moduleInstance) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        $path = $moduleInstance->path('assets' . DIRECTORY_SEPARATOR . 'system');

        return $this->getAsset($path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $module
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getModulePublicAsset(string $module, string $asset)
    {
        /** @var BaseModule $moduleInstance */
        $moduleInstance = app()->getModule($module);

        if (!$moduleInstance) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        $path = $moduleInstance->path('assets' . DIRECTORY_SEPARATOR . 'public');

        return $this->getAsset($path, $asset);
    }

    /**
     * Get asset from local storage.
     *
     * @param Request $request
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function getStorageAsset(Request $request, string $asset)
    {

        $name = $request->input('name');

        return $this->getAsset(storage_path('assets'), $asset, $name);
    }

    /**
     * Return asset by given full path if it exists.
     *
     * @param string $path
     * @param string $asset
     * @param string|null $name
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function getAsset(string $path, string $asset, ?string $name = null)
    {
        if (!$this->assetExists($path, $asset)) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        return $this->sendAsset($path, $asset, $name);
    }

    /**
     * Check if asset exists and user can view it.
     *
     * @param string $path
     * @param string $asset
     *
     * @return  bool
     */
    public function assetExists(string $path, string $asset)
    {
        return file_exists($path . DIRECTORY_SEPARATOR . $asset);
    }

    /**
     * Return asset file with headers.
     *
     * @param string $path
     * @param string $asset
     *
     * @return  mixed
     */
    public function sendAsset(string $path, string $asset, ?string $name = null)
    {
        $fullAssetPath = $path . DIRECTORY_SEPARATOR . $asset;

        $extension = strtolower(pathinfo($fullAssetPath, PATHINFO_EXTENSION));

        $type = $this->mimeTypes[$extension] ?? $this->defaultMimeType;

        if (in_array($extension, $this->noCacheExtensions, true)) {
            $cache = 'max-age=0, must-revalidate';
        } else {
            $cache = "max-age={$this->cacheLifeTime}, public";
        }

        return response()->file($fullAssetPath, [
            'Content-Type' => $type,
            'Cache-Control' => $cache,
            'Content-Disposition' => 'attachment; filename="' . ($name ?? $asset) . '"',
        ]);
    }

    /**
     * Return error response.
     *
     * @param string $asset
     *
     * @return  \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sendAssetNotFoundResponse(string $asset)
    {
        return response("File '$asset' not found.", 404);
    }
}
