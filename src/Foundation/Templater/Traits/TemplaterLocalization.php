<?php

namespace Core\Foundation\Templater\Traits;

use Core\Facades\Site;

trait TemplaterLocalization
{
    /** @var  string  Locale to translate captions. */
    protected $locale;

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return  $this
     */
    public function withLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Resolve localizations for all subjects.
     *
     * @return  $this
     */
    public function resolveLocalizations(): self
    {
        $template = [];

        if (isset($this->template['sections'])) {
            foreach ($this->template['sections'] as $section) {
                $template['sections'][] = $this->resolveCaption($section);
            }
        }
        if (isset($this->template['groups'])) {
            foreach ($this->template['groups'] as $group) {
                $template['groups'][] = $this->resolveCaption($group);
            }
        }
        if (isset($this->template['fields'])) {
            foreach ($this->template['fields'] as $field) {
                $template['fields'][] = $this->resolveCaption($field);
            }
        }

        $this->template = $template;

        return $this;
    }

    /**
     * Resolve caption for given subject.
     *
     * @param array|null $subject
     *
     * @return  array|null
     */
    protected function resolveCaption(?array $subject): ?array
    {
        if (!$subject) {
            return null;
        }

        if (isset($subject['caption'])) {
            return $subject;
        }

        if (isset($subject['caption_key'])) {
            $subject['caption'] = trans($subject['caption_key'], [], $this->getLocale());
        } else {
            $subject['caption'] = $subject['name'];
        }

        return $subject;
    }

    /**
     * Get locale.
     *
     * @return  string
     */
    protected function getLocale(): string
    {
        if (!$this->locale) {
            $this->locale = Site::getLocale();
        }

        return $this->locale;
    }
}