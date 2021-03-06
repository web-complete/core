<?php

namespace WebComplete\core\entity;

use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionMicroDbParser;
use WebComplete\core\factory\EntityFactory;
use WebComplete\microDb\MicroDb;

class AbstractEntityRepositoryMicro extends AbstractEntityRepository
{

    protected $collectionName = null;

    /**
     * @var MicroDb
     */
    protected $microDb;
    /**
     * @var ConditionMicroDbParser
     */
    protected $conditionParser;

    /**
     * @param EntityFactory $factory
     * @param MicroDb $microDb
     * @param ConditionMicroDbParser $conditionParser
     */
    public function __construct(EntityFactory $factory, MicroDb $microDb, ConditionMicroDbParser $conditionParser)
    {
        parent::__construct($factory);
        $this->microDb = $microDb;
        $this->conditionParser = $conditionParser;
    }

    /**
     * @param \Closure $closure
     *
     * @throws \Exception
     */
    public function transaction(\Closure $closure)
    {
        $closure();
    }

    /**
     * @param $id
     *
     * @return AbstractEntity|null
     * @throws \WebComplete\microDb\Exception
     */
    public function findById($id)
    {
        return $this->findOne($this->createCondition(['id' => $id]));
    }

    /**
     * @param Condition $condition
     *
     * @return AbstractEntity|null
     * @throws \WebComplete\microDb\Exception
     */
    public function findOne(Condition $condition)
    {
        $result = null;
        $row = $this->microDb->getCollection($this->collectionName)->fetchOne(
            $this->conditionParser->filter($condition),
            $this->conditionParser->sort($condition)
        );
        if ($row) {
            /** @var AbstractEntity $result */
            $result = $this->factory->create();
            $result->mapFromArray($row);
        }

        return $result;
    }

    /**
     * @param Condition $condition
     *
     * @return AbstractEntity[]
     * @throws \WebComplete\microDb\Exception
     */
    public function findAll(Condition $condition = null): array
    {
        $result = [];
        $rows = $this->microDb->getCollection($this->collectionName)->fetchAll(
            $this->conditionParser->filter($condition, $limit, $offset),
            $this->conditionParser->sort($condition),
            $limit,
            $offset
        );
        foreach ($rows as $row) {
            /** @var AbstractEntity $entity */
            $entity = $this->factory->create();
            $entity->mapFromArray($row);
            $result[$entity->getId()] = $entity;
        }

        return $result;
    }

    /**
     * @param Condition|null $condition
     *
     * @return int
     * @throws \WebComplete\microDb\Exception
     */
    public function count(Condition $condition = null): int
    {
        return \count($this->findAll($condition));
    }

    /**
     * @param AbstractEntity $item
     * @throws \WebComplete\microDb\Exception
     */
    public function save(AbstractEntity $item)
    {
        $collection = $this->microDb->getCollection($this->collectionName);
        if ($id = (string)$item->getId()) {
            $collection->update(function ($row) use ($id) {
                return isset($row['id']) && (string)$row['id'] === $id;
            }, $item->mapToArray());
        } else {
            $id = $collection->insert($item->mapToArray());
            $item->setId($id);
        }
    }

    /**
     * @param $id
     * @throws \WebComplete\microDb\Exception
     */
    public function delete($id)
    {
        $id = (string)$id;
        $this->microDb->getCollection($this->collectionName)->delete(function ($row) use ($id) {
            return isset($row['id']) && (string)$row['id'] === $id;
        });
    }

    /**
     * @param Condition|null $condition
     * @throws \WebComplete\microDb\Exception
     */
    public function deleteAll(Condition $condition = null)
    {
        $filter = $this->conditionParser->filter($condition);
        if (!$filter) {
            $filter = function () {
                return true;
            };
        }
        $this->microDb->getCollection($this->collectionName)->delete($filter);
    }

    /**
     * @param string $field
     * @param string $key
     * @param Condition|null $condition
     *
     * @return array
     * @throws \WebComplete\microDb\Exception
     * @throws \TypeError
     */
    public function getMap(string $field, string $key = 'id', Condition $condition = null): array
    {
        $result = [];
        $items = $this->findAll($condition);
        foreach ($items as $item) {
            $data = $item->mapToArray();
            if (isset($data[$key])) {
                $result[$data[$key]] = $data[$field] ?? null;
            }
        }
        return $result;
    }
}
