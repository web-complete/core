<?php

namespace WebComplete\core\utils\typecast;

use WebComplete\core\utils\typecast\type\CastInterface;
use WebComplete\core\utils\typecast\type\Factory;
use WebComplete\core\utils\typecast\type\Scheme;
use WebComplete\core\utils\typecast\type\TypeArrayOfType;

if (!\function_exists('\WebComplete\core\utils\typecast\cast')) {
    /**
     * @param $value
     * @param CastInterface|string|array $config
     *
     * @param bool $strict
     *
     * @return mixed|null
     */
    function cast($value, $config, bool $strict = false)
    {
        $factory = new Factory();
        if (\is_array($config)) {
            /** @var TypeArrayOfType $arrayOfType */
            $config = ($arrayOfType = $factory->checkArrayOfType($config))
                ? $arrayOfType
                : new Scheme($config, $factory, $strict);
        } elseif (!$config instanceof CastInterface) {
            $config = $factory->createType($config);
        }

        $cast = new Cast($config);
        return $cast->process($value);
    }
}
