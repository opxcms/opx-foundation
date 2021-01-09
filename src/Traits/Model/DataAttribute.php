<?php

namespace Core\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Exception;
use JsonException;

/**
 * Trait to work with data attributes
 *
 * @property Model $this
 */
trait DataAttribute
{
    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (strpos($key, '_') === 0) {
            return $this->getAttributeFromData($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (strpos($key, '_') === 0) {
            $this->setAttributeToData($key, $value);
            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get '$key' field from data attribute of model
     *
     * @param string $key
     *
     * @return  mixed|null
     */
    private function getAttributeFromData(string $key)
    {
        $data = $this->getData();

        if (empty($data)) {
            return null;
        }

        return $data[$key] ?? null;
    }

    /**
     * Set '$key' field to data attribute of model
     *
     * @param string $key
     * @param mixed $value
     *
     * @return  void
     */
    private function setAttributeToData(string $key, $value): void
    {
        $data = $this->getData();

        $data[$key] = $value;

        $this->setAttribute('data', $data);
    }

    /**
     * Convert data field to JSON before saving model
     *
     * @param array $options
     *
     * @return  void
     *
     * @throws JsonException
     */
    public function save(array $options = []): void
    {
        if (isset($this->data) && is_array($this->data)) {
            $this->data = json_encode($this->data, JSON_THROW_ON_ERROR);
        }

        parent::save($options);
    }

    /**
     * Get data converted to array.
     *
     * @return  array
     */
    private function getData(): array
    {
        $data = $this->getAttribute('data');

        if (!is_array($data)) {
            try {
                $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                $data = [];
            }
        }

        return $data ?? [];
    }
}