<?php

namespace Core\Foundation\Templater\Traits;

trait TemplaterFields
{
    /**
     * Get field by name.
     *
     * @param string $name
     *
     * @return  array|null
     */
    public function getField(string $name): ?array
    {
        if (!isset($this->template['fields'])) {
            return null;
        }

        foreach ($this->template['fields'] as $key => $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Set field by name.
     *
     * @param string $name
     * @param array $source
     * @param bool $append
     *
     * @return  bool
     */
    public function setField(string $name, array $source, bool $append = false): bool
    {
        if (empty($source) || !isset($this->template['fields'])) {
            return false;
        }

        foreach ($this->template['fields'] as $key => $field) {
            if ($field['name'] === $name) {
                $this->template['fields'][$key] = $source;
                return true;
            }
        }

        if ($append) {
            $this->template['fields'][] = $source;
            return true;
        }

        return false;
    }
}