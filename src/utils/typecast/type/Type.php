<?php

namespace WebComplete\core\utils\typecast\type;

abstract class Type implements CastInterface
{

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @param $default
     */
    public function __construct($default = null)
    {
        $this->default = $default;
    }
}
