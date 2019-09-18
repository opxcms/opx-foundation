<?php

namespace Core\Traits\Model;

use Modules\Opx\Image\OpxImage;
use Modules\Opx\MarkUp\OpxMarkUp;
use RuntimeException;

trait GetContent
{
    /**
     * Get content from field with optional markup parsing and image placement.
     *
     * @param string $contentField
     * @param bool $markUp
     * @param string|null $markUpClass
     * @param string|null $imagesField
     * @param int $imagesSize
     *
     * @return  string|null
     */
    public function getContent(string $contentField, bool $markUp = false, ?string $markUpClass = null, ?string $imagesField = null, int $imagesSize = 400): ?string
    {
        $content = $this->getAttribute($contentField);

        if ($content === null) {
            return null;
        }

        if ($imagesField !== null) {
            $content = preg_replace_callback('/\[img::([\d]+?)(.*|::.*)]/U', function ($matches) use ($imagesField, $imagesSize) {
                if (!isset($matches[1], $matches[2])) {
                    throw new RuntimeException("Wrong image format [{$matches[0]}]");
                }

                if (preg_match('/::([\d]+)$/', $matches[2], $size)) {
                    $imagesSize = (int)$size[1];
                }

                $additional = strpos($matches[2], '::') === 0 ? $matches[2] : null;
                $src = $this->getImageSrc($imagesField, $matches[1] - 1);
                $src = OpxImage::get($src, $imagesSize);

                return empty($src) ? null : "[img::{$src}{$additional}]";
            }, $content);
        }

        if ($markUp === true) {
            $content = OpxMarkUp::parse($content, $markUpClass);
        }

        return $content;
    }
}