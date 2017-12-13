<?php

namespace WebComplete\core\entity;

use function WebComplete\core\utils\typecast\cast;

abstract class AbstractEntity
{

    protected $id;

    private $data = [];

    /**
     * @return array
     */
    abstract public static function fields(): array;

    /**
     * @return int|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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

        if (isset($data['id'])) {
            $this->setId($data['id']);
        }
    }

    /**
     * @return array
     */
    public function mapToArray(): array
    {
        $result = [
            'id' => $this->getId()
        ];
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
     * @param $field
     * @param $value
     */
    public function set($field, $value)
    {
        $fields = static::fields();
        if (isset($fields[$field])) {
            $this->data[$field] = cast($value, $fields[$field]);
        }
    }

    /**
     * @param $field
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($field, $default = null)
    {
        return $this->data[$field] ?? $default;
    }
}
