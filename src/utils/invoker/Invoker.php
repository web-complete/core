<?php

namespace WebComplete\core\utils\invoker;


class Invoker implements InvokerInterface
{

    protected $adapted;


    public function __construct()
    {
        $this->adapted = new \Invoker\Invoker();
    }

    /**
     * Call the given function using the given parameters.
     *
     * @param callable $callable Function to call.
     * @param array $parameters Parameters to use.
     *
     * @return mixed Result of the function.
     *
     * @throws InvokerException
     */
    public function call($callable, array $parameters = array())
    {
        try {
            return $this->adapted->call($callable, $parameters);
        }
        catch (\Exception $e) {
            throw new InvokerException($e->getMessage(), $e->getCode());
        }
    }

}