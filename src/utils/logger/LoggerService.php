<?php

namespace WebComplete\core\utils\logger;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use WebComplete\core\utils\container\ContainerInterface;
use WebComplete\mvc\ApplicationConfig;

class LoggerService
{

    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var ApplicationConfig
     */
    protected $config;
    protected $scope = [];

    /**
     * @param ApplicationConfig $config
     * @param ContainerInterface $container
     */
    public function __construct(ApplicationConfig $config, ContainerInterface $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * @param string $name
     *
     * @return LoggerInterface
     */
    public function get(string $name): LoggerInterface
    {
        if (!isset($this->scope[$name])) {
            $this->scope[$name] = $this->create($name);
        }

        return $this->scope[$name];
    }

    /**
     * @param string $name
     *
     * @return LoggerInterface
     */
    protected function create(string $name): LoggerInterface
    {
        $configLogger = $this->config['logger'];
        $handlers = (array)($configLogger[$name] ?? []);
        return new Logger($name, $handlers);
    }
}
