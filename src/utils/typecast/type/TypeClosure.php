<?php

namespace WebComplete\core\utils\typecast\type;

class TypeClosure implements CastInterface
{
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function cast($value)
    {
        return ($this->closure)($value);
    }
}
