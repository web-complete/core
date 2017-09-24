<?php

namespace WebComplete\core\package;

abstract class AbstractPackage
{

    /**
     * @param $definitions
     *
     * @return void
     */
    abstract public function registerDependencies(array &$definitions);
}
