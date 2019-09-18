<?php

namespace Core\Foundation\Templater\Traits;


use Illuminate\Support\Facades\Validator;

trait TemplaterValidation
{
    /** @var  array  Validation errors. */
    protected $validationErrors;

    /**
     * Validate values.
     *
     * @param array $attrs
     *
     * @return  bool
     */
    public function validate($attrs = []): bool
    {
        // collect validation rules and values
        $fields = $this->collectFieldsToValidate($attrs);

        if ($fields === null) {
            return true;
        }

        // validate fields
        $validator = Validator::make($fields['values'], $fields['rules'], [], $fields['names']);

        // if validator fails
        if ($validator->fails()) {

            // collect errors
            $this->validationErrors = $validator->errors()->messages();

            // and return fail status
            return false;
        }

        // otherwise cleanup error messages
        $this->validationErrors = null;

        // and return success status
        return true;
    }

    /**
     * Get validation errors.
     *
     * @return  array|null
     */
    public function getValidationErrors(): ?array
    {
        return $this->validationErrors;
    }

    /**
     * Add validation error.
     *
     * @param string $field
     * @param string $error
     *
     * @return  $this
     */
    public function addValidationError(string $field, string $error): self
    {
        if (empty($this->validationErrors[$field])) {
            $this->validationErrors[$field] = [];
        }

        $this->validationErrors[$field][] = $error;

        return $this;
    }

    /**
     * Collect fields to validate and it's rules.
     *
     * @param array $attrs
     *
     * @return  array|null
     */
    protected function collectFieldsToValidate($attrs): ?array
    {
        if (empty($this->template['fields'])) {
            return null;
        }

        $rules = [];
        $values = [];
        $names = [];

        foreach ($this->template['fields'] as $field) {
            if (isset($field['validation']) && !empty($field['validation']) && (!isset($field['can_edit']) || $field['can_edit'] === true) && (!isset($field['can_view']) || $field['can_view'] === true)) {
                $rule = $field['validation'];
                if (count($attrs) > 0) {
                    foreach ($attrs as $key => $attr) {
                        if (strpos($rule, "%{$key}") !== false) {
                            $rule = str_replace("%{$key}", $attr, $rule);
                        }
                    }
                }
                $rules[$field['name']] = $rule;
                $values[$field['name']] = $this->mutateValue($field, $field['value'] ?? null, 'get');
                $names[$field['name']] = trans($field['caption']);
            }
        }

        return ['rules' => $rules, 'values' => $values, 'names' => $names];
    }
}