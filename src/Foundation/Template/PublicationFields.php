<?php

namespace Core\Foundation\Template;

trait PublicationFields
{
    public static function publicationPublished(string $placement = 'general/publication'): array
    {
        return self::checkbox('published', $placement, true);
    }

    public static function publicationPublishStart(string $placement = 'general/publication'): array
    {
        return self::datetime('publish_start', $placement);
    }

    public static function publicationPublishEnd(string $placement = 'general/publication'): array
    {
        return self::datetime('publish_end', $placement);
    }
}