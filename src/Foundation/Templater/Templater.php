<?php

namespace Core\Foundation\Templater;

use Core\Foundation\Templater\Traits\TemplaterAuthorization;
use Core\Foundation\Templater\Traits\TemplaterCleanup;
use Core\Foundation\Templater\Traits\TemplaterFields;
use Core\Foundation\Templater\Traits\TemplaterInput;
use Core\Foundation\Templater\Traits\TemplaterLocalization;
use Core\Foundation\Templater\Traits\TemplaterMutator;
use Core\Foundation\Templater\Traits\TemplaterValues;
use Core\Foundation\Templater\Traits\TemplaterValidation;

class Templater
{
    use TemplaterInput;
    use TemplaterAuthorization;
    use TemplaterLocalization;
    use TemplaterCleanup;
    use TemplaterValues;
    use TemplaterValidation;
    use TemplaterMutator;
    use TemplaterFields;

    /** @var  array  common template */
    protected $template;

    /**
     * Templater constructor.
     *
     * @param null|string|array $template
     *
     * @return  void
     */
    public function __construct($template = null)
    {
        if (is_string($template)) {
            $this->getTemplateFromFile($template);
        }

        if (is_array($template)) {
            $this->getTemplateFromArray($template);
        }
    }

    /**
     * Get template.
     *
     * @param bool $localize
     *
     * @return  array|null
     */
    public function get($localize = false): ?array
    {
        $this->resolvePermissions();

        $this->cleanUpEmpty();

        if ($localize) {
            $this->resolveLocalizations();
        }

        $this->mutateValues();

        return $this->template;
    }
}