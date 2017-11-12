<?php

namespace WebComplete\core\cube;

use WebComplete\core\utils\container\ContainerInterface;

abstract class AbstractCube
{

    /**
     * @param $definitions
     *
     * @return void
     */
    abstract public function registerDependencies(array &$definitions);

    /**
     * @return array [sort => migration class]
     */
    public function getMigrations(): array
    {
        return [];
    }

    /**
     */
    public function bootstrap(ContainerInterface $container)
    {
    }
}
