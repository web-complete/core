<?php

namespace WebComplete\core\utils\typecast\type;

class TypeArray extends Type
{

    /**
     * @param array $default
     */
    public function __construct($default = [])
    {
        parent::__construct($default);
    }

    /**
     * @param $value
     *
     * @return array|null
     */
    public function cast($value)
    {
        return $value === null ? $this->default : (array)$value;
    }
}
