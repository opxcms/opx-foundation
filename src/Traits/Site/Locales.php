<?php

namespace Core\Traits\Site;

use Illuminate\Support\Facades\Log;

trait Locales
{
    /**
     * Get current locale.
     *
     * @return  string
     */
    public function getLocale(): string
    {
        return $this->app->getLocale();
    }

    /**
     * Get installed locales.
     *
     * @return  array|null
     */
    public function installedLocales(): ?array
    {
        return $this->app['config']->get("lang.installed");
    }

    /**
     * Get current locale title.
     *
     * @return  string|null
     */
    public function getLocaleTitle(): ?string
    {
        $locale = $this->getLocale();

        return $this->installedLocales()[$locale] ?? null;
    }

    /**
     * Set current locale.
     *
     * @param string $locale
     * @param bool $skipCheck
     *
     * @return  boolean
     */
    public function setLocale(string $locale, $skipCheck = false): bool
    {
        $installed = $this->installedLocales();

        if (!$skipCheck && !array_key_exists($locale, $installed)) {
            $this->app->setLocale($this->app->getFallbackLocale());
            Log::error("Try to set missing locale '{$locale}'. Locale set to fallback");

            return false;
        }

        $this->app->setLocale($locale);

        return true;
    }

    /**
     * Get HTML string for language.
     *
     * @return  string
     */
    public function localeHtml(): string
    {
        $locale = $this->getLocale();

        return " lang=\"{$locale}\"";
    }

    /**
     * Render HTML code for locale selector.
     *
     * @param string|null $url
     *
     * @return  string|null
     */
    public function localeSelectorHtml($url = null): ?string
    {
        $installed = $this->installedLocales();

        if (!$installed) {
            return null;
        }

        $link = $url ?? url()->current();

        $html = '<ul>';
        foreach ($installed as $locale => $localeTitle) {
            $html .= "<li><a href=\"{$link}?locale={$locale}\">{$localeTitle}</a></li>";
        }
        $html .= '</ul>';

        return $html;
    }
}