<?php

namespace WebComplete\core\entity;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\utils\hydrator\HydratorInterface;
use WebComplete\core\condition\Condition;

abstract class AbstractEntityRepositoryDb extends AbstractEntityRepository
{
    const SERIALIZE_STRATEGY_JSON = 1;
    const SERIALIZE_STRATEGY_PHP  = 2;

    protected $table;
    /**
     * map: fieldName: propertyName
     * @var array|null
     */
    protected $map;
    protected $serializeFields = [];
    protected $serializeStrategy = self::SERIALIZE_STRATEGY_JSON;

    /** @var Connection */
    protected $db;

    /**
     * @var ConditionDbParser
     */
    protected $conditionParser;

    /**
     * @param ObjectFactory $factory
     * @param HydratorInterface $hydrator
     * @param ConditionDbParser $conditionParser
     * @param Connection $db
     */
    public function __construct(
        ObjectFactory $factory,
        HydratorInterface $hydrator,
        ConditionDbParser $conditionParser,
        Connection $db
    ) {
        parent::__construct($factory, $hydrator);
        $this->db = $db;
        $this->conditionParser = $conditionParser;
    }

    /**
     * @param \Closure $closure
     * @throws \Exception
     */
    public function transaction(\Closure $closure)
    {
        $this->db->transactional($closure);
    }

    /**
     * @param $id
     * @return AbstractEntity|null
     */
    public function findById($id)
    {
        return $this->findOne(new Condition(['t1.id' => $id]));
    }

    /**
     * @param Condition $condition
     * @return AbstractEntity|null
     */
    public function findOne(Condition $condition)
    {
        $result = null;
        $select = $this->selectQuery($condition);
        if ($row = $select->execute()->fetch()) {
            $result = $this->rowToEntity($row);
        }

        return $result;
    }

    /**
     * @param Condition $condition
     * @return AbstractEntity[]
     */
    public function findAll(Condition $condition = null): array
    {
        $result = [];
        $select = $this->selectQuery($condition);
        if ($rows = $select->execute()->fetchAll(\PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                $entity = $this->rowToEntity($row);
                $result[$entity->getId()] = $entity;
            }
        }

        return $result;
    }

    /**
     * @param Condition|null $condition
     * @return int
     */
    public function count(Condition $condition = null): int
    {
        $select = $this->selectQuery($condition);
        return $select->select(['t1.id'])->execute()->rowCount();
    }

    /**
     * @param AbstractEntity $item
     */
    public function save(AbstractEntity $item)
    {
        $data = $this->hydrator->extract($item, $this->map);
        $this->beforeDataSave($data);
        $this->serializeFields($data);
        if ($id = $item->getId()) {
            $this->db->update($this->table, $data, ['t1.id' => $id]);
        } else {
            unset($data['id']);
            $this->db->insert($this->table, $data);
            $item->setId((int)$this->db->lastInsertId());
        }
    }

    /**
     * @param $id
     *
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function delete($id)
    {
        $this->db->delete($this->table, ['t1.id' => $id]);
    }

    /**
     * @param Condition|null $condition
     */
    public function deleteAll(Condition $condition = null)
    {
        $query = $this->selectQuery($condition);
        $query->delete($this->table, 't1');
        $query->execute();
    }

    /**
     * @param string $field
     * @param string $key
     * @param Condition|null $condition
     *
     * @return array
     * @throws \Exception
     */
    public function getMap(string $field, string $key = 'id', Condition $condition = null): array
    {
        $result = [];
        $select = $this->selectQuery($condition);
        $select->select(['t1.' . $field, 't1.' . $key]);
        if ($rows = $select->execute()->fetchAll(\PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                $this->unserializeFields($row);
                $result[$row[$key]] = $row[$field];
            }
        }

        return $result;
    }

    /**
     * @param Condition|null $condition
     * @return QueryBuilder
     */
    protected function selectQuery(Condition $condition = null): QueryBuilder
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select(['t1.*'])->from($this->table, 't1');
        $this->conditionParser->parse($queryBuilder, $condition, $this->map);
        return $queryBuilder;
    }

    /**
     * Adjust data before save
     * @param $data
     */
    protected function beforeDataSave(&$data)
    {
    }

    /**
     * @param $data
     *
     * @return AbstractEntity
     */
    private function rowToEntity($data)
    {
        $this->unserializeFields($data);
        $entity = $this->factory->createFromData($data, $this->map);
        /** @var AbstractEntity $entity */
        return $entity;
    }

    /**
     * @param $data
     */
    private function serializeFields(&$data)
    {
        foreach ($this->serializeFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->serializeStrategy === self::SERIALIZE_STRATEGY_JSON
                    ? \json_encode($data[$field])
                    : \serialize($data[$field]);
            }
        }
    }

    /**
     * @param $row
     */
    private function unserializeFields(&$row)
    {
        foreach ($this->serializeFields as $field) {
            if (isset($row[$field])) {
                $row[$field] = $this->serializeStrategy === self::SERIALIZE_STRATEGY_JSON
                    ? \json_decode($row[$field], true)
                    : \unserialize($row[$field], ['allowed_classes' => true]);
            }
        }
    }
}
