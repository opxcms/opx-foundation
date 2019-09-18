<?php

namespace Core\Foundation\Templater\Traits;

trait TemplaterInput
{
    /**
     * Get template from file.
     *
     * @param string $filename
     *
     * @return  $this
     */
    public function getTemplateFromFile(string $filename): self
    {
        if (!$this->template) {
            $this->template = [];
        }

        $this->template = array_merge_recursive($this->template, require $filename);

        return $this;
    }

    /**
     * Get template from array.
     *
     * @param array $array
     *
     * @return  $this
     */
    public function getTemplateFromArray(array $array): self
    {
        if (!$this->template) {
            $this->template = [];
        }

        $this->template = array_merge_recursive($this->template, $array);

        return $this;
    }
}