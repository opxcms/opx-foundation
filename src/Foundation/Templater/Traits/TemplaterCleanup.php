<?php

namespace Core\Foundation\Templater\Traits;

trait TemplaterCleanup
{
    /**
     * Cleanup empty subjects and unused sections and groups.
     *
     * @return  $this
     */
    public function cleanUpEmpty(): self
    {
        // If no fields in template it means template is empty.

        if (empty($this->template['fields']) || !is_array($this->template['fields'])) {
            $this->template = [];

            return $this;
        }

        // First remove empty sections, groups and fields.

        if (!empty($this->template['sections'])) {
            $this->template['sections'] = $this->removeEmptySubjects($this->template['sections']);
        }
        if (!empty($this->template['groups'])) {
            $this->template['groups'] = $this->removeEmptySubjects($this->template['groups']);
        }
        if (!empty($this->template['fields'])) {
            $this->template['fields'] = $this->removeEmptySubjects($this->template['fields']);
        }

        // Second get names of sections and groups present in template

        $initialSections = $this->getSubjectsNames($this->template['sections'] ?? []);
        $initialGroups = $this->getSubjectsNames($this->template['groups'] ?? []);

        $usedSections = [];
        $usedGroups = [];

        // Cleanup fields placed in nonexistent groups and sections
        // and list of sections and groups used in fields

        foreach ($this->template['fields'] as $key => $field) {

            $section = empty($field['section']) ? null : $field['section'];
            $group = empty($field['group']) ? null : $field['group'];

            $sectionOK = $section === null || in_array($section, $initialSections, true);
            $groupOK = $group === null || in_array($group, $initialGroups, true);

            if ($sectionOK && $groupOK) {
                if ($section !== null) {
                    $usedSections[] = $section;
                }
                if ($group !== null) {
                    $usedGroups[] = $group;
                }
            } else {
                unset($this->template['fields'][$key]);
            }
        }

        // Cleanup unused sections

        if (!empty($this->template['sections'])) {
            foreach ($this->template['sections'] as $key => $section) {
                if (!in_array($section['name'], $usedSections, true)) {
                    unset($this->template['sections'][$key]);
                }
            }
        }

        // Cleanup unused groups
        if (!empty($this->template['groups'])) {
            foreach ($this->template['groups'] as $key => $group) {
                if (!in_array($group['name'], $usedGroups, true)) {
                    unset($this->template['groups'][$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Remove empty items from subject.
     *
     * @param array $subject
     *
     * @return  array
     */
    private function removeEmptySubjects(array $subject): array
    {
        $passed = [];

        foreach ($subject as $item) {
            if (!empty($item)) {
                $passed[] = $item;
            }
        }

        return $passed;
    }

    /**
     * Get names of items containing in subject.
     *
     * @param $subject
     *
     * @return  array
     */
    private function getSubjectsNames($subject): array
    {
        $names = [];

        if (is_array($subject)) {
            foreach ($subject as $item) {
                $names[] = $item['name'];
            }
        }

        return $names;
    }
}