<?php

namespace Core\Foundation\Templater\Traits\Mutators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Core\Foundation\Templater\Traits\Mutators\Helpers\StoreFile;

class AudioMutator implements MutatorInterface
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

        foreach ($value as $audio) {
            $src = $audio['src'] ?? '';

            if (!empty($audio['file']) || !empty($audio['external'])) {

                $src = self::writeFile(
                    $src,
                    $audio['file'],
                    $storage,
                    $field['path'],
                    $field['prefix'] ?? 'audio_',
                    isset($audio['external'])
                );
            }

            $new[] = [
                'src' => $src,
            ];
        }

        return json_encode($new);
    }
}