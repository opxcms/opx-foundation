<?php

namespace Core\Foundation\Template;

class Template
{
    use StringFields,
        IdFields,
        DateTimeFields,
        CheckFields,
        ImageFields,
        SelectFields,
        PropertiesFields,
        RelatedFields,
        SeoFields,
        TimestampsFields,
        PublicationFields;

    /**
     * Make section record.
     *
     * @param string $id
     * @param string $permissions
     *
     * @return  array
     */
    public static function section(string $id, string $permissions = ''): array
    {
        return self::makeRecord($id, 'section', $permissions);
    }

    /**
     * Make group record.
     *
     * @param string $id
     * @param string $permissions
     *
     * @return  array
     */
    public static function group(string $id, string $permissions = ''): array
    {
        return self::makeRecord($id, 'group', $permissions);
    }

    /**
     * Make field record.
     *
     * @param string $id
     * @param string $placement
     * @param string $type
     * @param mixed $default
     * @param string $validation
     * @param string $permissions
     * @param string $info
     * @param array $fields
     *
     * @return  array
     */
    protected static function makeField(string $id, string $placement, string $type, $default, string $info, string $validation, string $permissions, array $fields = []): array
    {
        if (strpos($placement, '/') !== false) {
            [$section, $group] = explode('/', $placement);
        }

        $fields = array_merge($fields, [
            'section' => $section ?? '',
            'group' => $group ?? '',
            'type' => $type,
            'default' => $default,
            'info' => empty($info) ? null : $info,
        ]);

        return self::makeRecord($id, 'field', $permissions, $validation, $fields);
    }

    /**
     * Make record.
     *
     * @param string $id
     * @param string $subject
     * @param string $permissions
     * @param array $fields
     * @param string $validation
     *
     * @return  array
     */
    protected static function makeRecord(string $id, string $subject, string $permissions, string $validation = '', array $fields = []): array
    {
        if (strpos($id, '::') !== false) {
            [$module, $name] = explode('::', $id, 2);
            $caption = "{$module}::template.{$subject}_{$name}";
        } else {
            $name = $id;
            $caption = "{$subject}s.{$subject}_{$name}";
        }

        if (strpos($permissions, '|') !== false) {
            [$readPermission, $writePermission] = explode('|', $permissions, 2);
        }

        $record = [
            'name' => $name,
            'caption' => $caption,
            'permissions' => [
                'view' => $readPermission ?? '',
                'edit' => $writePermission ?? '',
            ],
        ];

        if (!empty($fields)) {
            $record = array_merge($record, $fields);
        }

        if (!empty($validation)) {
            $record['validation'] = $validation;
        }

        return $record;
    }
}