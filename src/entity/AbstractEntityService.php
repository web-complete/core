<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;
use WebComplete\core\utils\paginator\Paginator;

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
     * @param array $data
     *
     * @return AbstractEntity
     */
    public function createFromData(array $data): AbstractEntity
    {
        return $this->repository->createFromData($data);
    }

    /**
     * @param Paginator $paginator
     * @param Condition $condition
     * @return AbstractEntity[]
     */
    public function list(Paginator $paginator, Condition $condition): array
    {
        if (!$paginator->getTotal()) {
            if (!$total = $this->count($condition)) {
                return [];
            }
            $paginator->setTotal($total);
        }
        $condition->limit($paginator->getLimit());
        $condition->offset($paginator->getOffset());
        return $this->findAll($condition);
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

    /**
     * @param Condition|null $condition
     *
     * @return mixed
     */
    public function deleteAll(Condition $condition = null)
    {
        return $this->repository->deleteAll($condition);
    }

    /**
     * @param string $field
     * @param string $key
     * @param Condition|null $condition
     *
     * @return array
     * @throws \TypeError
     */
    public function getMap(string $field, string $key = 'id', Condition $condition = null): array
    {
        return $this->repository->getMap($field, $key, $condition);
    }
}
