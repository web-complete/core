<?php

namespace WebComplete\core\cube;

abstract class AbstractCube
{

    /**
     * @param $definitions
     *
     * @return void
     */
    abstract public function registerDependencies(array &$definitions);
}
