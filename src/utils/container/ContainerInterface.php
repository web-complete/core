<?php

namespace WebComplete\core\utils\container;

interface ContainerInterface extends \Psr\Container\ContainerInterface
{

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @param $value
     */
    public function set($id, $value);

    /**
     * Make a new instance of definition
     *
     * @param $name
     * @return mixed
     */
    public function make($name);
}
