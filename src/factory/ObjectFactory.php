<?php

namespace WebComplete\core\factory;

use WebComplete\core\utils\container\ContainerInterface;
use WebComplete\core\utils\hydrator\HydratorInterface;

/**
 * Class ObjectFactory
 */
class ObjectFactory extends AbstractFactory
{

    /**
     * @var string
     */
    protected $objectClass;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param ContainerInterface $container
     * @param HydratorInterface $hydrator
     * @param string|null $objectClass
     */
    public function __construct(
        ContainerInterface $container,
        HydratorInterface $hydrator,
        string $objectClass = null
    ) {
        parent::__construct($container);
        if ($objectClass !== null) {
            $this->objectClass = $objectClass;
        }
        $this->hydrator = $hydrator;
    }

    /**
     * @return object
     */
    public function create()
    {
        return $this->make($this->objectClass);
    }

    /**
     * @param array $data
     * @param array|null $map
     *
     * @return object
     */
    public function createFromData(array $data, array $map = null)
    {
        $object = $this->create();
        $this->hydrator->hydrate($data, $object, $map);
        return $object;
    }
}
