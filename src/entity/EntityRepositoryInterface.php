<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;


interface EntityRepositoryInterface
{

    /**
     * @param \Closure $closure
     */
    public function transaction(\Closure $closure);

    /**
     * @return AbstractEntity
     */
    public function create();

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
    public function findAll(Condition $condition);

    /**
     * @param Condition $condition
     * @return int
     */
    public function count(Condition $condition);

    /**
     * @param AbstractEntity $item
     */
    public function save(AbstractEntity$item);

    /**
     * @param $id
     */
    public function delete($id);

}