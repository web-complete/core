<?php

namespace WebComplete\core\utils\invoker;


interface InvokerInterface
{

    /**
     * Call the given function using the given parameters.
     *
     * @param callable $callable   Function to call.
     * @param array    $parameters Parameters to use.
     *
     * @return mixed Result of the function.
     *
     * @throws InvokerException
     */
    public function call($callable, array $parameters = array());

}