<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;
use WebComplete\core\factory\EntityFactory;

abstract class AbstractEntityRepository implements EntityRepositoryInterface
{

    /**
     * @var EntityFactory
     */
    protected $factory;

    /**
     * @param EntityFactory $factory
     */
    public function __construct(EntityFactory $factory)
    {
        $this->factory = $factory;
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
     *
     * @return AbstractEntity
     */
    public function createFromData(array $data): AbstractEntity
    {
        /** @var AbstractEntity $result */
        $result = $this->factory->create();
        $result->mapFromArray($data);
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
