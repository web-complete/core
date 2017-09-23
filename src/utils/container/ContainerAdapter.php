<?php

namespace WebComplete\core\utils\container;

class ContainerAdapter implements ContainerInterface
{
    private $container;

    /**
     * @param object $container
     */
    public function __construct($container = null)
    {
        $this->container = $container;
    }

    /**
     * @param object $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    public function get($id)
    {
        return $this->call('get', $id);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    public function has($id)
    {
        return $this->call('has', $id);
    }

    /**
     * @param $id
     * @return mixed
     * @throws ContainerException
     */
    public function make($id)
    {
        return $this->call('make', $id);
    }

    /**
     * @param $method
     * @param $id
     *
     * @return mixed
     * @throws ContainerException
     */
    protected function call($method, $id)
    {
        if (!$this->container) {
            throw new ContainerException('Container not injected');
        }
        if (\method_exists($this->container, $method)) {
            return $this->container->$method($id);
        }
        throw new ContainerException('Container method not found: ' . $method);
    }
}
