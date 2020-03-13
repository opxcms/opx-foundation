<?php

namespace Core\Foundation\Templater\Traits\Mutators;

use Illuminate\Support\Facades\Storage;

class ImageMutator implements MutatorInterface
{
    /**
     * Transform field on getting value from template.
     *
     * @param mixed $value
     *
     * @return  mixed
     */
    public static function get($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    /**
     * Transform value on setting it to template.
     *
     * @param mixed $value
     * @param array $field
     *
     * @return  mixed
     */
    public static function set($value, $field)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        $new = [];

        /** @var \Illuminate\Filesystem\Filesystem $storage */
        $storage = Storage::disk(empty($field['public']) ? 'secure' : 'public');

        foreach ($value as $image) {
            $src = $image['src'] ?? '';

            if (!empty($image['file']) || !empty($image['external'])) {

                if (!empty($image['external'])) {
                    $image_content = file_get_contents($src);
                } else {
                    $image_parts = explode(';base64,', $image['file']);
                    $image_content = base64_decode($image_parts[1]);
                }

                $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));
                $localPath = $field['path'];
                $diskPath = $storage->getDriver()->getAdapter()->getPathPrefix();
                $prefix = $field['prefix'] ?? 'img_';
                $filename = self::unique_filename($diskPath . DIRECTORY_SEPARATOR . $localPath, $prefix, $extension);

                $localFilename = $field['path'] . DIRECTORY_SEPARATOR . $filename;

                $storage->put($localFilename, $image_content);

                $src = $storage->url($localFilename);
            }

            $new[] = [
                'src' => $src,
                'alt' => $image['alt'] ?? '',
                'description' => $image['description'] ?? '',
            ];
        }

        return json_encode($new);
    }

    /**
     * Converts large hexidecimal numbers into decimal strings.
     *
     * @param string $dir
     * @param string $prefix
     * @param string $ext
     *
     * @return  string
     */
    private static function unique_filename(string $dir, string $prefix = '', string $ext = ''): string
    {
        do {
            $filename = $prefix . strtolower(str_random());
            if ($ext) {
                $filename .= '.' . $ext;
            }
        } while (file_exists($dir . DIRECTORY_SEPARATOR . $filename));

        return $filename;
    }
}