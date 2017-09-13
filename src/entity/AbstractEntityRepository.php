<?php

namespace WebComplete\core\entity;

use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\utils\hydrator\HydratorInterface;


abstract class AbstractEntityRepository implements EntityRepositoryInterface
{

    /**
     * @var ObjectFactory
     */
    protected $factory;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;


    public function __construct(ObjectFactory $factory, HydratorInterface $hydrator)
    {
        $this->factory = $factory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return AbstractEntity|object
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * Adjust data before save
     * @param $data
     */
    abstract protected function beforeDataSave(&$data);

}