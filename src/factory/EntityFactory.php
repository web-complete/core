<?php

namespace WebComplete\core\factory;

abstract class EntityFactory extends AbstractFactory
{

    /**
     * @var string
     */
    protected $objectClass;

    /**
     * @return object
     */
    public function create()
    {
        return $this->make($this->objectClass);
    }
}
