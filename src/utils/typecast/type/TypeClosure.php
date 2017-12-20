<?php

namespace WebComplete\core\utils\typecast\type;

class TypeClosure implements CastInterface
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function cast($value)
    {
        return \call_user_func($this->callable, $value);
    }
}
