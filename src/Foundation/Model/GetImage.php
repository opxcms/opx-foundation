<?php

namespace Core\Traits\Model;

trait GetImage
{
    /**
     * Get src of image.
     *
     * @param string $field
     * @param int|null $index
     *
     * @return  string|array|null
     */
    public function getImageSrc($field = 'image', $index = 0)
    {
        $img = $this->getAttribute($field);

        if ($img === null) {
            return null;
        }

        if (!is_array($img)) {
            $img = json_decode($img, true);
        }

        if ($index !== null) {
            return $img[$index]['src'] ?? null;
        }

        return array_map(static function ($image) {
            return $image['src'];
        }, $img);
    }
}