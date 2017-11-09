<?php

namespace WebComplete\core\utils\typecast\type;

interface CastInterface
{

    /**
     * @param $value
     *
     * @return mixed
     */
    public function cast($value);
}
