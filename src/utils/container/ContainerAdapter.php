<?php

namespace WebComplete\core\utils\container;


class ContainerAdapter implements ContainerInterface
{

    private $container;

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
        if(!$this->container) {
            throw new ContainerException('Container not injected');
        }
        if(method_exists($this->container, 'get')) {
            return $this->container->get($id);
        }
        else {
            throw new ContainerException('Container method not found: get');
        }
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    public function has($id)
    {
        if(!$this->container) {
            throw new ContainerException('Container not injected');
        }
        if(method_exists($this->container, 'has')) {
            return $this->container->has($id);
        }
        else {
            throw new ContainerException('Container method not found: has');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws ContainerException
     */
    public function make($id)
    {
        if(!$this->container) {
            throw new ContainerException('Container not injected');
        }
        if(method_exists($this->container, 'make')) {
            return $this->container->make($id);
        }
        else {
            throw new ContainerException('Container method not found: make');
        }
    }

}