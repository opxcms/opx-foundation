<?php

namespace Core\Foundation\Templater\Traits\Mutators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Core\Foundation\Templater\Traits\Mutators\Helpers\StoreFile;

class FileMutator implements MutatorInterface
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

        foreach ($value as $file) {
            $src = $file['src'] ?? '';

            if (!empty($file['file']) || !empty($file['external'])) {

                $src = self::writeFile(
                    $src,
                    $file['file'],
                    $storage,
                    $field['path'],
                    $field['prefix'] ?? 'file_',
                    isset($file['external'])
                );
            }

            $new[] = [
                'src' => $src,
            ];
        }

        return json_encode($new);
    }
}