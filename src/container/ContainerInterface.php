<?php

namespace WebComplete\core\container;


interface ContainerInterface extends \Psr\Container\ContainerInterface
{

    /**
     * Make a new instance of definition
     *
     * @param $name
     * @return mixed
     */
    public function make($name);

}