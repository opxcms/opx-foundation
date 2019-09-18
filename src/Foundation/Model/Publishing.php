<?php

namespace Core\Traits\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait Publishing
{
    /**
     * Check if model is published.
     *
     * @param string|null $published
     * @param string|null $publishStart
     * @param string|null $publishEnd
     *
     * @return  bool
     */
    public function isPublished(?string $published = 'published', ?string $publishStart = 'publish_start', ?string $publishEnd = 'publish_end'): bool
    {
        $now = Carbon::now();

        $isPublished = $published !== null ? $this->getAttribute($published) : true;

        $publicationStarted = $publishStart !== null ? $this->getAttribute($publishStart) === null || ($this->getAttribute($publishStart) <= $now) : true;

        $publicationNotEnded = $publishEnd !== null ? $this->getAttribute($publishEnd) === null || ($this->getAttribute($publishEnd) >= $now) : true;

        return $isPublished && $publicationStarted && $publicationNotEnded;
    }

    /**
     * Add published conditions to query.
     *
     * @param Builder $query
     * @param string $published
     * @param string $publishStart
     * @param string $publishEnd
     *
     * @return  mixed
     */
    public static function addPublishingToQuery($query, string $published = 'published', string $publishStart = 'publish_start', string $publishEnd = 'publish_end')
    {
        $now = Carbon::now();

        return $query
            ->where($published, 1)
            ->where(static function (Builder $query) use ($now, $publishStart) {
                $query->whereNull($publishStart)
                    ->orWhere($publishStart, '<=', $now);
            })
            ->where(static function (Builder $query) use ($now, $publishEnd) {
                $query->whereNull($publishEnd)
                    ->orWhere($publishEnd, '>=', $now);
            });
    }

    /**
     * Scope a query to only published.
     *
     * @param Builder $query
     * @param string $published
     * @param string $publishStart
     * @param string $publishEnd
     *
     * @return Builder
     */
    public function scopePublished($query, string $published = 'published', string $publishStart = 'publish_start', string $publishEnd = 'publish_end'): Builder
    {
        return self::addPublishingToQuery($query, $published, $publishStart, $publishEnd);
    }

    /**
     * Publish model.
     *
     * @param string $published
     * @param string $publishStart
     * @param string $publishEnd
     *
     * @return  void
     */
    public function publish(?string $published = 'published', ?string $publishStart = 'publish_start', ?string $publishEnd = 'publish_end'): void
    {
        $now = Carbon::now();

        $isPublished = $published !== null ? $this->getAttribute($published) : null;
        $publicationStart = $publishStart !== null ? $this->getAttribute($publishStart) : null;
        $publicationEnd = $publishEnd !== null ? $this->getAttribute($publishEnd) : null;

        if ($isPublished !== null && !$isPublished) {
            $this->setAttribute($published, true);
        }

        if ($publicationStart !== null && $publicationStart >= $now) {
            $this->setAttribute($publishStart, null);
        }

        if ($publicationEnd !== null && $publicationEnd <= $now) {
            $this->setAttribute($publishEnd, null);
        }
    }

    /**
     * Mark model as unpublished.
     *
     * @param string $published
     *
     * @return  void
     */
    public function unPublish(string $published = 'published'): void
    {
        $this->setAttribute($published, false);
    }
}