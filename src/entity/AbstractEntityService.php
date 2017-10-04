<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;

abstract class AbstractEntityService implements EntityRepositoryInterface
{

    /**
     * @var EntityRepositoryInterface
     */
    protected $repository;

    /**
     * @param EntityRepositoryInterface $repository
     */
    public function __construct(EntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Proxy method
     * @param \Closure $closure
     * @throws \Exception
     */
    public function transaction(\Closure $closure)
    {
        return $this->repository->transaction($closure);
    }

    /**
     * Proxy method
     * @return AbstractEntity
     */
    public function create(): AbstractEntity
    {
        return $this->repository->create();
    }

    /**
     * Proxy method
     *
     * @param array $data
     * @param array|null $map
     *
     * @return AbstractEntity
     */
    public function createFromData(array $data, array $map = null): AbstractEntity
    {
        return $this->repository->createFromData($data, $map);
    }

    /**
     * Proxy method
     * @param $id
     * @return AbstractEntity|null
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Proxy method
     * @param Condition $condition
     * @return AbstractEntity|null
     */
    public function findOne(Condition $condition)
    {
        return $this->repository->findOne($condition);
    }

    /**
     * Proxy method
     * @param Condition $condition
     * @return AbstractEntity[]
     */
    public function findAll(Condition $condition = null): array
    {
        return $this->repository->findAll($condition);
    }

    /**
     * Proxy method
     * @param Condition|null $condition
     * @return int
     */
    public function count(Condition $condition = null): int
    {
        return $this->repository->count($condition);
    }

    /**
     * Proxy method
     * @param AbstractEntity $item
     */
    public function save(AbstractEntity $item)
    {
        return $this->repository->save($item);
    }

    /**
     * Proxy method
     * @param $id
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
