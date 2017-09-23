<?php

namespace WebComplete\core\factory;

use WebComplete\core\condition\Condition;

/**
 * Class ConditionFactory
 */
class ConditionFactory extends AbstractFactory
{

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->make(Condition::class);
    }
}
