<?php

namespace WebComplete\core\cube;

use WebComplete\core\utils\container\ContainerInterface;

abstract class AbstractCube
{
    public $enabled = true;
    public $bootstrapAfterCubeClass = null;

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

    /**
     * @param $definitions
     *
     * @return void
     */
    public function registerDependencies(array &$definitions)
    {
    }
}
