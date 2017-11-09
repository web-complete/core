<?php

namespace WebComplete\core\utils\typecast;

use WebComplete\core\utils\typecast\type\CastInterface;

class Cast
{
    const INT = 'type_int';
    const BOOL = 'type_bool';
    const FLOAT = 'type_float';
    const STRING = 'type_string';
    const BINARY = 'type_binary';
    const OBJECT = 'type_object';
    const UNSET = 'type_unset';
    const ARRAY = 'type_array';

    const TYPES = [self::INT, self::BOOL, self::FLOAT, self::STRING,
        self::BINARY, self::OBJECT, self::UNSET, self::ARRAY];

    /**
     * @var CastInterface
     */
    protected $type;

    /**
     * @param CastInterface $type
     */
    public function __construct(CastInterface $type)
    {
        $this->type = $type;
    }

    /**
     * @param $value
     *
     * @return mixed|null
     */
    public function process($value)
    {
        return $this->type->cast($value);
    }
}
