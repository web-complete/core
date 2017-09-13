<?php

namespace WebComplete\core\entity;

use Doctrine\DBAL\Connection;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\utils\hydrator\HydratorInterface;
use WebComplete\core\condition\Condition;


abstract class AbstractEntityEntityRepositoryDb extends AbstractEntityRepository
{

    const SERIALIZE_STRATEGY_JSON = 1;
    const SERIALIZE_STRATEGY_PHP  = 2;

    protected $table;
    protected $serializeFields = [];
    protected $serializeStrategy = self::SERIALIZE_STRATEGY_JSON;

    /** @var Connection */
    protected $db;

    /**
     * @var ConditionDbParser
     */
    protected $conditionParser;


    public function __construct(
        ObjectFactory $factory,
        HydratorInterface $hydrator,
        ConditionDbParser $conditionParser,
        Connection $db)
    {
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
     * @return AbstractEntity|object|null
     */
    public function findById($id)
    {
        $result = null;
        $select = $this->selectQuery()->where('t1 = :id')->setParameter(':id', $id);
        if($row = $select->execute()->fetch()) {
            $this->unserializeFields($row);
            $result = $this->factory->createFromData($row);
        }

        return $result;
    }

    /**
     * @param Condition $condition
     * @return AbstractEntity|object|null
     */
    public function findOne(Condition $condition)
    {
        $result = null;
        $select = $this->selectQuery($condition);
        if($row = $select->execute()->fetch()) {
            $this->unserializeFields($row);
            $result = $this->factory->createFromData($row);
        }

        return $result;
    }

    /**
     * @param Condition $condition
     * @return AbstractEntity[]
     */
    public function findAll(Condition $condition)
    {
        $result = [];
        $select = $this->selectQuery($condition);
        if($rows = $select->execute()->fetchAll(\PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                $this->unserializeFields($row);
                /** @var AbstractEntity $entity */
                $entity = $this->factory->createFromData($row);
                $result[$entity->getId()] = $entity;
            }
        }

        return $result;
    }

    /**
     * @param Condition $condition
     * @return int
     */
    public function count(Condition $condition)
    {
        $select = $this->selectQuery($condition);
        return $select->select(['id'])->execute()->rowCount();
    }

    /**
     * @param AbstractEntity $item
     */
    public function save(AbstractEntity $item)
    {
        $data = $this->hydrator->extract($item);
        $this->beforeDataSave($data);
        $this->serializeFields($data);
        if($id = $item->getId()) {
            $this->db->update($this->table, $data, ['id' => $id]);
        }
        else {
            unset($data['id']);
            $this->db->insert($this->table, $data);
            $item->setId($this->db->lastInsertId());
        }
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * @param Condition|null $condition
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function selectQuery(Condition $condition = null)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select(['t1.*'])->from($this->table, 't1');
        $this->conditionParser->parse($queryBuilder, $condition);
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
     */
    private function serializeFields(&$data)
    {
        foreach ($this->serializeFields as $field) {
            if(isset($data[$field])) {
                $data[$field] = $this->serializeStrategy == self::SERIALIZE_STRATEGY_JSON
                    ? json_encode($data[$field])
                    : serialize($data[$field]);
            }
        }
    }

    /**
     * @param $row
     */
    private function unserializeFields(&$row)
    {
        foreach ($this->serializeFields as $field) {
            if(isset($row[$field])) {
                $row[$field] = $this->serializeStrategy == self::SERIALIZE_STRATEGY_JSON
                    ? json_decode($row[$field], true)
                    : unserialize($row[$field]);
            }
        }
    }

}