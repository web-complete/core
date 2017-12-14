<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;

interface EntityRepositoryInterface
{

    /**
     * @param \Closure $closure
     * @throws \Exception
     */
    public function transaction(\Closure $closure);

    /**
     * @return AbstractEntity
     */
    public function create(): AbstractEntity;

    /**
     * @param array $data
     *
     * @return AbstractEntity
     */
    public function createFromData(array $data): AbstractEntity;

    /**
     * @param $id
     * @return AbstractEntity|null
     */
    public function findById($id);

    /**
     * @param Condition $condition
     * @return AbstractEntity|null
     */
    public function findOne(Condition $condition);

    /**
     * @param Condition $condition
     * @return AbstractEntity[]
     */
    public function findAll(Condition $condition = null): array;

    /**
     * @param Condition|null $condition
     * @return int
     */
    public function count(Condition $condition = null): int;

    /**
     * @param AbstractEntity $item
     */
    public function save(AbstractEntity$item);

    /**
     * @param $id
     */
    public function delete($id);

    /**
     * @param Condition|null $condition
     */
    public function deleteAll(Condition $condition = null);

    /**
     * @param string $field
     * @param string $key
     * @param Condition|null $condition
     *
     * @return array
     * @throws \TypeError
     */
    public function getMap(string $field, string $key = 'id', Condition $condition = null): array;

    /**
     * @param array $conditions
     *
     * @return Condition
     */
    public function createCondition(array $conditions = []): Condition;
}
