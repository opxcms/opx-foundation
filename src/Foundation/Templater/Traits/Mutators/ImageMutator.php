<?php

namespace Core\Foundation\Templater\Traits\Mutators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Core\Foundation\Templater\Traits\Mutators\Helpers\StoreFile;

class ImageMutator implements MutatorInterface
{
    use StoreFile;

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

        /** @var Filesystem $storage */
        $storage = Storage::disk(empty($field['public']) ? 'secure' : 'public');

        foreach ($value as $image) {
            $src = $image['src'] ?? '';

            if (!empty($image['file']) || !empty($image['external'])) {

                $src = self::writeFile(
                    $src,
                    $image['file'],
                    $storage,
                    $field['path'],
                    $field['prefix'] ?? 'file_',
                    isset($image['external'])
                );
            }

            $new[] = [
                'src' => $src,
                'alt' => $image['alt'] ?? '',
                'description' => $image['description'] ?? '',
            ];
        }

        return json_encode($new);
    }
}