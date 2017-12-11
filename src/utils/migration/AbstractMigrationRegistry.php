<?php

namespace WebComplete\core\utils\migration;

use RuntimeException;
use WebComplete\core\utils\container\ContainerInterface;

abstract class AbstractMigrationRegistry implements MigrationRegistryInterface
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $class
     *
     * @return MigrationInterface
     * @throws \Exception
     */
    public function getMigration($class): MigrationInterface
    {
        try {
            $migration = $this->container->get($class);
            if (!$migration || !$migration instanceof MigrationInterface) {
                throw new \RuntimeException('Migration must implement ' . MigrationInterface::class);
            }
        } catch (\Exception $e) {
            throw new RuntimeException('Migration instantiate error: ' . $class, 0, $e);
        }
        return $migration;
    }
}
