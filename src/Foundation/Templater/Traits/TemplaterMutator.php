<?php

namespace Core\Foundation\Templater\Traits;

trait TemplaterMutator
{
    public function mutateValues(): self
    {
        if (!isset($this->template['fields'])) {
            return $this;
        }

        foreach ($this->template['fields'] as $key => $field) {

            $type = $field['type'] ?? null;

            if ($type === null) {
                continue;
            }

            $mutator = 'Core\\Foundation\\Templater\\Traits\\Mutators\\' . title_case($type) . 'Mutator';

            if (class_exists($mutator)) {
                $this->template['fields'][$key]['value'] = call_user_func([$mutator, 'get'], $field['value'] ?? null);
            }

        }

        return $this;
    }

    /**
     * @param array $field
     * @param mixed $value
     * @param string $method
     *
     * @return  mixed
     */
    protected function mutateValue(array $field, $value, string $method)
    {
        $type = $field['type'] ?? null;

        if ($type !== null) {
            $mutator = 'Core\\Foundation\\Templater\\Traits\\Mutators\\' . title_case($type) . 'Mutator';

            if (class_exists($mutator)) {
                $value = call_user_func([$mutator, $method], $value, $field);
            }
        }

        return $value;
    }
}