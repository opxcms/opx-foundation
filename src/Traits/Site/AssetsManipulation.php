<?php

namespace Core\Traits\Site;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

trait AssetsManipulation
{
    /**
     * Push scripts to assets array.
     *
     * @param  string|array $scripts
     * @param  integer $priority Lower values has higher priority
     *
     * @return  $this
     */
    public function addAssetScript($scripts, int $priority = 1): self
    {
        if (!is_array($scripts)) {
            $scripts = [$scripts];
        }

        $this->scripts[$priority] = array_merge($this->scripts[$priority] ?? [], $scripts);

        return $this;
    }

    /**
     * Set asset scripts.
     *
     * @param array|null $scripts
     *
     * @return $this
     */
    public function setAssetScripts(array $scripts = null): self
    {
        $this->scripts = $scripts;

        return $this;
    }

    /**
     * Push css to assets array.
     *
     * @param  string|array $styles
     * @param  integer $priority Lower values has higher priority
     *
     * @return  $this
     */
    public function addAssetStyle($styles, $priority = 1): self
    {
        if (!is_array($styles)) {
            $styles = [$styles];
        }

        $this->styles[$priority] = array_merge($this->styles[$priority] ?? [], $styles);

        return $this;
    }

    /**
     * Set asset scripts.
     *
     * @param array|null $styles
     *
     * @return $this
     */
    public function setAssetStyles(array $styles = null): self
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * Render scripts array to HTML.
     *
     * @return string|null
     */
    public function scripts(): ?string
    {
        if (empty($this->scripts)) {
            return null;
        }

        // Resolve priority for scripts and remove duplicates.
        // If some script has several priorities use highest one.
        $scripts = [];

        foreach ($this->getAssetScripts() as $priority) {
            if (is_array($priority)) {
                foreach ($priority as $script) {
                    if (!in_array($script, $scripts, true)) {
                        $scripts[] = $script;
                    }
                }
            }
        }

        $html = '';

        foreach ($scripts as $script) {
            $html .= '<script src="' . $script . '"></script>' . "\n";
        }

        return $html;
    }

    /**
     * Get array of asset scripts.
     *
     * @param  integer|null $priority
     *
     * @return  array|null
     */
    public function getAssetScripts($priority = null): ?array
    {
        if (!is_array($this->scripts)) {
            return null;
        }

        ksort($this->scripts);

        if ($priority === null) {
            return $this->scripts ?? null;
        }

        return $this->scripts[$priority] ?? null;
    }

    /**
     * Render css array to HTML.
     *
     * @return string|null
     */
    public function styles(): ?string
    {
        $styles = $this->stylesArray();

        if (empty($styles)) {
            return null;
        }

        $html = '';

        foreach ($styles as $style) {
            $html .= '<link rel="stylesheet" href="' . $style . '">' . "\n";
        }

        return $html;
    }

    /**
     * Get flattened array of styles.
     *
     * @return  array|null
     */
    protected function stylesArray(): ?array
    {
        if (empty($this->styles)) {
            return null;
        }

        // Resolve priority for styles and remove duplicates.
        // If some style has several priorities use highest one.
        $styles = [];

        foreach ($this->getAssetStyles() as $priority) {
            if (is_array($priority)) {
                foreach ($priority as $style) {
                    if (!in_array($style, $styles, true)) {
                        $styles[] = $style;
                    }
                }
            }
        }

        return $styles;
    }

    /**
     * Get array of asset css.
     *
     * @param  integer|null $priority
     *
     * @return  array|null
     */
    public function getAssetStyles($priority = null): ?array
    {
        if (!is_array($this->styles)) {
            return null;
        }

        ksort($this->styles);

        if ($priority === null) {
            return $this->styles ?? null;
        }

        return $this->styles[$priority] ?? null;
    }

    /**
     * Render styles to inject.
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function injectStyles(): string
    {
        if (empty($this->injectStyles)) {
            return '';
        }

        $buffer = '<style>';

        foreach ($this->injectStyles as $style) {
            if (Storage::disk('public')->exists($style)) {
                $buffer .= Storage::disk('public')->get($style);
            }
        }

        $buffer .= '</style>';

        return $buffer;
    }

    /**
     * Add styles to inject.
     *
     * @param  string|array $styles
     *
     * @return  $this
     */
    public function addInjectStyle($styles): self
    {
        if (!is_array($styles)) {
            $styles = [$styles];
        }

        $this->injectStyles = array_merge($this->injectStyles ?? [], $styles);

        return $this;
    }

    /**
     * Get script to load styles.
     *
     * @return  string
     */
    public function stylesAsync(): string
    {
        $styles = $this->stylesArray();

        if (empty($styles)) {
            return '';
        }

        $html = '<script>' . "\r\n";
        $html .= 'const head = document.getElementsByTagName("head")[0];' . "\r\n";
        $html .= 'var link = document.createElement("link");' . "\r\n";
        $html .= 'link.rel = "stylesheet";' . "\r\n";
        $html .= 'link.type = "text/css";' . "\r\n";
        $html .= 'link.media = "all";' . "\r\n";
        foreach ($styles as $style) {
            $html .= "link.href = \"{$style}\";" . "\r\n";
            $html .= 'head.appendChild(link);' . "\r\n";
        }
        $html .= '</script>' . "\r\n";

        return $html;
    }
}