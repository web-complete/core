<?php

namespace WebComplete\core\utils\traits;

use function WebComplete\core\utils\typecast\cast;

trait TraitData
{

    private $data = [];

    /**
     * @return array
     */
    abstract public static function fields(): array;


    /**
     * @param array $data
     * @param bool $update
     */
    public function mapFromArray(array $data, bool $update = false)
    {
        if (!$update) {
            $this->data = [];
        }

        foreach ($data as $field => $value) {
            $this->set($field, $value);
        }
    }

    /**
     * @return array
     */
    public function mapToArray(): array
    {
        $result = [];
        $fields = \array_keys(static::fields());
        foreach ($fields as $field) {
            $result[$field] = $this->get($field);
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $field
     * @param $value
     */
    public function set(string $field, $value)
    {
        $fields = static::fields();
        if (isset($fields[$field])) {
            $this->data[$field] = cast($value, $fields[$field]);
        }
    }

    /**
     * @param string $field
     * @param null $default
     *
     * @return mixed|null
     */
    public function get(string $field, $default = null)
    {
        return $this->data[$field] ?? $default;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function has(string $field): bool
    {
        return isset(static::fields()[$field]);
    }
}
