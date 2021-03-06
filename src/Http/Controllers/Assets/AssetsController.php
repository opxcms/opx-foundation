<?php

namespace Core\Http\Controllers\Assets;

use Core\Foundation\Module\BaseModule;
use Core\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        'mp3' => 'audio/mpeg',
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
     * @return  ResponseFactory|mixed
     */
    public function getSystemAsset(Request $request, string $asset)
    {
        $path = app()->getAssetsPath('manage/assets/system');

        return $this->getAsset($request, $path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function getPublicAsset(Request $request, string $asset)
    {
        $path = app()->getAssetsPath('manage/assets/public');

        return $this->getAsset($request, $path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $module
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function getModuleSystemAsset(Request $request, string $module, string $asset)
    {
        /** @var BaseModule $moduleInstance */
        $moduleInstance = app()->getModule($module);

        if (!$moduleInstance) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        $path = $moduleInstance->path('assets' . DIRECTORY_SEPARATOR . 'system');

        return $this->getAsset($request, $path, $asset);
    }

    /**
     * Return requested asset file if user can get it.
     *
     * @param string $module
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function getModulePublicAsset(Request $request, string $module, string $asset)
    {
        /** @var BaseModule $moduleInstance */
        $moduleInstance = app()->getModule($module);

        if (!$moduleInstance) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        $path = $moduleInstance->path('assets' . DIRECTORY_SEPARATOR . 'public');

        return $this->getAsset($request, $path, $asset);
    }

    /**
     * Get asset from local storage.
     *
     * @param Request $request
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function getStorageAsset(Request $request, string $asset)
    {

        $name = $request->input('name');

        return $this->getAsset($request, storage_path('assets'), $asset, $name);
    }

    /**
     * Get asset from temporary folder.
     *
     * @param Request $request
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function getTempAsset(Request $request, string $asset)
    {
        $name = $request->input('name');

        return $this->getAsset($request, app()->storagePath() . DIRECTORY_SEPARATOR . 'temp', $asset, $name);
    }

    /**
     * Return asset by given full path if it exists.
     *
     * @param string $path
     * @param string $asset
     * @param string|null $name
     *
     * @return  ResponseFactory|mixed
     */
    public function getAsset(Request $request, string $path, string $asset, ?string $name = null)
    {
        if (!$this->assetExists($path, $asset)) {
            return $this->sendAssetNotFoundResponse($asset);
        }

        return $this->sendAsset($request, $path, $asset, $name);
    }

    /**
     * Check if asset exists and user can view it.
     *
     * @param string $path
     * @param string $asset
     *
     * @return  bool
     */
    public function assetExists(string $path, string $asset): bool
    {
        return file_exists($path . DIRECTORY_SEPARATOR . $asset);
    }

    /**
     * Return asset file with headers.
     *
     * @param string $path
     * @param string $asset
     * @param string|null $name
     *
     * @return  mixed
     */
    public function sendAsset(Request $request, string $path, string $asset, ?string $name = null)
    {
        $fullAssetPath = $path . DIRECTORY_SEPARATOR . $asset;
        $extension = strtolower(pathinfo($fullAssetPath, PATHINFO_EXTENSION));

        $headers = [
            'Content-Type' => $this->mimeTypes[$extension] ?? $this->defaultMimeType,
            'Cache-Control' => in_array($extension, $this->noCacheExtensions, true) ? 'max-age=0, must-revalidate' : "max-age={$this->cacheLifeTime}, public",
        ];

        if ($name !== null) {
            $headers['Content-Disposition'] = 'attachment; filename="' . $name . '"';
        }

        return response()->file($fullAssetPath, $headers);
    }

    /**
     * Return error response.
     *
     * @param string $asset
     *
     * @return  ResponseFactory|mixed
     */
    public function sendAssetNotFoundResponse(string $asset)
    {
        return response("File '$asset' not found.", 404);
    }

    /**
     * Write asset to temp dir.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postTempAsset(Request $request): JsonResponse
    {
        $dir = app()->storagePath() . DIRECTORY_SEPARATOR . 'temp';
        $part = $request->input('part', 0);
        $original = $request->input('original');
        $extension = $original ? '.' . pathinfo($original, PATHINFO_EXTENSION) : '';

        if ($part === 0) {
            $filename = self::makeUniqueFilename($dir, $extension);
        } else {
            $filename = $request->input('filename');
            if (!file_exists($dir . DIRECTORY_SEPARATOR . $filename)) {
                return response()->json(['message' => 'Error writing file. Probably first part is missing'], 404);
            }
        }

        $fullName = $dir . DIRECTORY_SEPARATOR . $filename;

        $content = $request->input('content');
        $fileParts = explode(';base64,', $content);
        $content = base64_decode($fileParts[1] ?? $content);

        $written = file_put_contents($fullName, $content, FILE_APPEND);

        if ($written === false) {
            return response()->json(['message' => "Error writing file [{$fullName}]."], 404);
        }

        $size = filesize($fullName);

        return response()->json([
            'filename' => $filename,
            'src' => "manage/assets/temp/{$filename}",
            'written' => $written,
            'total' => $size,
        ]);
    }

    /**
     * Generate unique filename based on random string.
     *
     * @param string $directory
     * @param string $extension
     *
     * @return  string
     */
    protected static function makeUniqueFilename(string $directory, string $extension = ''): string
    {
        do {
            $filename = Str::random()(16) . ($extension ? '.' . $extension : '');
        } while (file_exists($directory . DIRECTORY_SEPARATOR . $filename));

        return $filename;
    }
}
