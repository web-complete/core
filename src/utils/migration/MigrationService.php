<?php

namespace WebComplete\core\utils\migration;

class MigrationService
{

    /**
     * @var MigrationRegistryInterface
     */
    protected $registry;

    /**
     * MigrationService constructor.
     *
     * @param MigrationRegistryInterface $registry
     */
    public function __construct(MigrationRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param array $classes
     *
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function upAll(array $classes)
    {
        foreach ($classes as $class) {
            $this->up($class);
        }
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function downAll()
    {
        $classes = $this->registry->getRegistered();
        foreach ($classes as $class) {
            $this->down($class);
        }
    }

    /**
     * @param $class
     *
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function up($class)
    {
        $this->registry->register($class);
    }

    /**
     * @param $class
     *
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function down($class)
    {
        $this->registry->unregister($class);
    }

    /**
     * @return array
     */
    public function getRegistered(): array
    {
        return $this->registry->getRegistered();
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function isRegistered(string $class): bool
    {
        return $this->registry->isRegistered($class);
    }
}
