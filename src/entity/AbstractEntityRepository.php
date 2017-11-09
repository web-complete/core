<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;
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

    /**
     * @param ObjectFactory $factory
     * @param HydratorInterface $hydrator
     */
    public function __construct(ObjectFactory $factory, HydratorInterface $hydrator)
    {
        $this->factory = $factory;
        $this->hydrator = $hydrator;
    }

    /**
     * @return AbstractEntity
     */
    public function create(): AbstractEntity
    {
        $result = $this->factory->create();
        /** @var AbstractEntity $result */
        return $result;
    }

    /**
     * @param array $data
     * @param array|null $map
     *
     * @return AbstractEntity
     */
    public function createFromData(array $data, array $map = null): AbstractEntity
    {
        $result = $this->factory->createFromData($data, $map);
        /** @var AbstractEntity $result */
        return $result;
    }

    /**
     * Adjust data before save
     * @param $data
     */
    abstract protected function beforeDataSave(&$data);

    /**
     * @param array $conditions
     *
     * @return Condition
     */
    protected function createCondition(array $conditions = []): Condition
    {
        return new Condition($conditions);
    }
}
