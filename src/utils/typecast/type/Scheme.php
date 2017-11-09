<?php

namespace WebComplete\core\utils\typecast\type;

class Scheme implements CastInterface
{
    /**
     * @var CastInterface[]
     */
    protected $config = [];
    /**
     * @var Factory
     */
    protected $factory;
    /**
     * @var bool
     */
    protected $strict;

    /**
     * @param array $config
     * @param Factory|null $factory
     * @param bool $strict
     *
     * @throws \WebComplete\core\utils\typecast\Exception
     */
    public function __construct(array $config = [], Factory $factory = null, bool $strict = false)
    {
        $this->factory = $factory ?? new Factory();
        $this->strict = $strict;
        $this->config = $this->parseConfig($config);
    }

    /**
     * @param array $config
     *
     * @return array
     * @throws \WebComplete\core\utils\typecast\Exception
     */
    protected function parseConfig(array $config): array
    {
        $result = [];
        foreach ($config as $key => $type) {
            if (\is_array($type)) {
                if ($arrayOfType = $this->factory->checkArrayOfType($type)) {
                    $result[$key] = $arrayOfType;
                } else {
                    $result[$key] = new self($type, $this->factory, $this->strict);
                }
            } elseif ($type instanceof CastInterface) {
                $result[$key] = $type;
            } else {
                $result[$key] = $this->factory->createType($type);
            }
        }

        return $result;
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function cast($value): array
    {
        $result = [];
        $config = $this->config;
        foreach ((array)$value as $key => $itemValue) {
            if (isset($config[$key])) {
                $result[$key] = $config[$key]->cast($itemValue);
                unset($config[$key]);
            } elseif (!$this->strict) {
                $result[$key] = $itemValue;
            }
        }

        if ($this->strict) {
            foreach ($config as $key => $type) {
                $result[$key] = $type->cast(null);
            }
        }

        return $result;
    }
}
