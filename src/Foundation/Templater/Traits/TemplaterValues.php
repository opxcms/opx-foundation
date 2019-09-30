<?php

namespace Core\Foundation\Templater\Traits;

use Illuminate\Http\Request;

trait TemplaterValues
{
    /**
     * Fill values from array.
     *
     * @param array $source
     * @param string|array|null $except
     *
     * @return  $this
     */
    public function fillValuesFromArray(array $source, $except = null): self
    {
        if (!isset($this->template['fields'])) {
            return $this;
        }

        if (is_string($except)) {
            $except = [$except];
        }

        foreach ($this->template['fields'] as $key => $field) {
            if ($except === null || !in_array($field['name'], $except, true)) {
                $this->setValue($key, $source[$field['name']] ?? null);
            }
        }

        return $this;
    }

    /**
     * Fill values from object.
     *
     * @param mixed $source
     * @param string|array|null $except
     *
     * @return  $this
     */
    public function fillValuesFromObject($source, $except = null): self
    {
        if (empty($source) || !isset($this->template['fields'])) {
            return $this;
        }

        if (is_string($except)) {
            $except = [$except];
        }

        foreach ($this->template['fields'] as $key => $field) {
            if ($except === null || !in_array($field['name'], $except, true)) {
                $this->setValue($key, $source->{$field['name']} ?? null);
            }
        }

        return $this;
    }

    /**
     * Fill values from request.
     *
     * @param Request $request
     * @param string|array|null $except
     *
     * @return  $this
     */
    public function fillValuesFromRequest(Request $request, $except = null): self
    {
        if (!isset($this->template['fields'])) {
            return $this;
        }

        if (is_string($except)) {
            $except = [$except];
        }

        $values = $request->all();

        foreach ($this->template['fields'] as $key => $field) {
            if ($except === null || !in_array($field['name'], $except, true)) {
                $this->setValue($key, $values[$field['name']] ?? null);
            }
        }

        return $this;
    }

    /**
     * Fill values with defaults.
     *
     * @param bool $force
     *
     * @return  $this
     */
    public function fillDefaults(bool $force = false): self
    {
        if (!isset($this->template['fields'])) {
            return $this;
        }

        foreach ($this->template['fields'] as $key => $field) {
            if (isset($field['default'])
                && ($force
                    || !isset($this->template['fields'][$key]['value'])
                    || empty($this->template['fields'][$key]['value']))
            ) {
                $this->template['fields'][$key]['value'] = $field['default'];
            }
        }

        return $this;
    }

    /**
     * Set specified values.
     *
     * @param array $values
     *
     * @return  $this
     */
    public function setValues(array $values): self
    {
        if (!isset($this->template['fields'])) {
            return $this;
        }

        foreach ($this->template['fields'] as $key => $field) {
            if (array_key_exists($field['name'], $values)) {
                $this->setValue($key, $values[$field['name']]);
            }
        }

        return $this;
    }

    /**
     * Set value in template.
     *
     * @param  $key
     * @param mixed $value
     *
     * @return  void
     */
    protected function setValue($key, $value): void
    {
        if (!isset($this->template['fields'][$key]['can_edit']) || $this->template['fields'][$key]['can_edit'] === true) {

            $this->template['fields'][$key]['value'] = $this->mutateValue($this->template['fields'][$key], $value, 'set');
        }
    }

    /**
     * Get values for fields that can be edited.
     *
     * @param mixed|null $key
     *
     * @return  array|mixed|null
     */
    public function getEditableValues($key = null)
    {
        if (!isset($this->template['fields'])) {
            return null;
        }

        $values = [];

        foreach ($this->template['fields'] as $field) {
            if ((!isset($field['can_edit']) || $field['can_edit'] === true) && (!isset($field['can_view']) || $field['can_view'] === true)) {
                $values[$field['name']] = $field['value'];
            }
        }

        if ($key === null) {
            return $values;
        }

        return $values[$key] ?? null;
    }
}