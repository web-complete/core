<?php

namespace WebComplete\core\utils\migration;

interface MigrationRegistryInterface
{

    /**
     * check or create initial registry
     */
    public function initRegistry();

    /**
     * @return string[] migration classes
     */
    public function getRegistered(): array;

    /**
     * @param string $class migration class
     * @return bool
     */
    public function isRegistered(string $class): bool;

    /**
     * @param string $class migration class
     */
    public function register(string $class);

    /**
     * @param string $class migration class
     */
    public function unregister(string $class);
}
