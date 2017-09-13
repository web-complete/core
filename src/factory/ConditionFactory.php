<?php

namespace WebComplete\core\factory;

use WebComplete\core\condition\Condition;
use WebComplete\core\utils\container\ContainerInterface;

/**
 * Class ConditionFactory
 */
class ConditionFactory extends AbstractFactory
{

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->make(Condition::class);
    }

}