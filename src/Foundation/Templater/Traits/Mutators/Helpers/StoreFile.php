<?php

namespace Core\Foundation\Templater\Traits\Mutators\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Str;

trait StoreFile
{
    /**
     * Generate unique filename based on random string.
     *
     * @param string $directory
     * @param string $prefix
     * @param string $extension
     *
     * @return  string
     */
    protected static function makeUniqueFilename(string $directory, string $prefix = '', string $extension = ''): string
    {
        do {
            $filename = $prefix . strtolower(Str::random()) . ($extension ? '.' . $extension : '');
        } while (file_exists($directory . DIRECTORY_SEPARATOR . $filename));

        return $filename;
    }

    /**
     * Write file to disk.
     *
     * @param string $original
     * @param string|null $content
     * @param Filesystem $storage
     * @param string $pathOnDisk
     * @param string $prefix
     * @param bool $isExternal
     *
     * @return  string|null
     */
    protected static function writeFile(string $original, ?string $content, Filesystem $storage, string $pathOnDisk, string $prefix = 'file_', bool $isExternal = false): ?string
    {
        if (empty($content) && !$isExternal) {
            return null;
        }

        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $diskPath = $storage->getDriver()->getAdapter()->getPathPrefix();
        $filename = self::makeUniqueFilename($diskPath . DIRECTORY_SEPARATOR . $pathOnDisk, $prefix, $extension);
        $localFilename = $pathOnDisk . DIRECTORY_SEPARATOR . $filename;

        if (strpos($original, 'manage/assets/temp/') === 0) {
            $original = app()->storagePath() . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . pathinfo($original, PATHINFO_BASENAME);
            rename($original, $diskPath . $localFilename);
        } elseif ($isExternal) {
            copy($original, $diskPath . DIRECTORY_SEPARATOR . $localFilename);
        } else {
            $fileParts = explode(';base64,', $content);
            $fileContent = base64_decode($fileParts[1]);
            $storage->put($localFilename, $fileContent);
        }

        return $storage->url($localFilename);
    }
}