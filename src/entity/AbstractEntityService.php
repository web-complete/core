<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;
use WebComplete\core\utils\event\EventService;
use WebComplete\core\utils\paginator\Paginator;

abstract class AbstractEntityService implements EntityRepositoryInterface
{
    const EVENT_SAVE_BEFORE       = 'entity_save_before';
    const EVENT_SAVE_AFTER        = 'entity_save_after';
    const EVENT_DELETE_BEFORE     = 'entity_delete_before';
    const EVENT_DELETE_AFTER      = 'entity_delete_after';
    const EVENT_DELETE_ALL_BEFORE = 'entity_delete_all_before';
    const EVENT_DELETE_ALL_AFTER  = 'entity_delete_all_after';

    /**
     * @var EntityRepositoryInterface
     */
    protected $repository;
    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @param EntityRepositoryInterface $repository
     * @param EventService $eventService
     */
    public function __construct(EntityRepositoryInterface $repository, EventService $eventService)
    {
        $this->repository = $repository;
        $this->eventService = $eventService;
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
     * @param AbstractEntity $item
     * @param array $oldData
     */
    public function save(AbstractEntity $item, array $oldData = [])
    {
        $eventData = ['service' => $this, 'item' => $item, 'oldData' => $oldData];
        $this->eventService->trigger(self::EVENT_SAVE_BEFORE, $eventData);
        $this->repository->save($item);
        $this->eventService->trigger(self::EVENT_SAVE_AFTER, $eventData);
    }

    /**
     * Proxy method
     * @param $id
     */
    public function delete($id)
    {
        $eventData = ['service' => $this, 'id' => $id];
        $this->eventService->trigger(self::EVENT_DELETE_BEFORE, $eventData);
        $this->repository->delete($id);
        $this->eventService->trigger(self::EVENT_DELETE_AFTER, $eventData);
    }

    /**
     * @param Condition|null $condition
     */
    public function deleteAll(Condition $condition = null)
    {
        $eventData = ['service' => $this, 'condition' => $condition];
        $this->eventService->trigger(self::EVENT_DELETE_ALL_BEFORE, $eventData);
        $this->repository->deleteAll($condition);
        $this->eventService->trigger(self::EVENT_DELETE_ALL_AFTER, $eventData);
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

    /**
     * @param array $conditions
     *
     * @return Condition
     */
    public function createCondition(array $conditions = []): Condition
    {
        return $this->repository->createCondition($conditions);
    }
}
