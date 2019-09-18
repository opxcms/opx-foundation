<?php

namespace Core\Foundation\Templater\Traits\Mutators;

interface MutatorInterface
{
    /**
     * Transform field on getting value from template.
     *
     * @param mixed $value
     *
     * @return  mixed
     */
    public static function get($value);

    /**
     * Transform value on setting it to template.
     *
     * @param mixed $value
     * @param array $field
     *
     * @return  mixed
     */
    public static function set($value, $field);
}