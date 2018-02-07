<?php

namespace WebComplete\core\factory;

use WebComplete\core\utils\container\ContainerInterface;

/**
 * Class AbstractFactory
 */
abstract class AbstractFactory
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get an instance
     *
     * @param $name
     * @return mixed
     */
    protected function get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Create an instance
     *
     * @param $name
     * @return mixed
     */
    protected function make($name)
    {
        return $this->container->make($name);
    }
}
