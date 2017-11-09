<?php

namespace WebComplete\core\utils\typecast\type;

class TypeArrayOfType implements CastInterface
{
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
     * @return array
     */
    public function cast($value): array
    {
        $result = [];
        foreach ((array)$value as $key => $itemValue) {
            $result[$key] = $this->type->cast($itemValue);
        }
        return $result;
    }
}
