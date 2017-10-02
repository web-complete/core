<?php

namespace WebComplete\core\condition;

use Doctrine\DBAL\Query\QueryBuilder;

class ConditionDbParser
{

    /** @var array */
    protected $map = [];

    /**
     * @param QueryBuilder $queryBuilder
     * @param Condition|null $condition
     * @param array|null $map
     */
    public function parse(QueryBuilder $queryBuilder, Condition $condition = null, array $map = null)
    {
        if ($condition === null) {
            return;
        }
        if ($map) {
            $this->map = \array_flip($map);
        }
        $this->parseConditions($queryBuilder, $condition);

        foreach ($condition->getSort() as $field => $direction) {
            $queryBuilder->orderBy($this->mapField($field), $direction === \SORT_DESC ? 'desc' : 'asc');
        }

        if ($offset = (int)$condition->getOffset()) {
            $queryBuilder->setFirstResult($offset);
        }

        if ($limit = (int)$condition->getLimit()) {
            $queryBuilder->setMaxResults($limit);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Condition $condition
     */
    protected function parseConditions(QueryBuilder $queryBuilder, Condition $condition)
    {
        foreach ($condition->getConditions() as $cond) {
            if (!\is_array($cond)) {
                continue;
            }

            $cond = \array_values($cond);
            $type = \array_shift($cond);
            switch ($type) {
                case Condition::EQUALS:
                    $this->parseSimpleCondition('=', $queryBuilder, $cond);
                    break;
                case Condition::NOT_EQUALS:
                    $this->parseSimpleCondition('<>', $queryBuilder, $cond);
                    break;
                case Condition::LESS_THAN:
                    $this->parseSimpleCondition('<', $queryBuilder, $cond);
                    break;
                case Condition::GREATER_THAN:
                    $this->parseSimpleCondition('>', $queryBuilder, $cond);
                    break;
                case Condition::LESS_OR_EQUALS:
                    $this->parseSimpleCondition('<=', $queryBuilder, $cond);
                    break;
                case Condition::GREATER_OR_EQUALS:
                    $this->parseSimpleCondition('>=', $queryBuilder, $cond);
                    break;
                case Condition::BETWEEN:
                    $this->parseBetweenCondition($queryBuilder, $cond);
                    break;
                case Condition::LIKE:
                    $this->parseLikeCondition($queryBuilder, $cond);
                    break;
                case Condition::IN:
                    $this->parseInCondition($queryBuilder, $cond);
                    break;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $cond
     */
    protected function parseLikeCondition(QueryBuilder $queryBuilder, $cond)
    {
        $value = $cond[2] ? '%' : '';
        $value .= $cond[1];
        $value .= $cond[3] ? '%' : '';
        $field = $this->mapField($cond[0]);
        $queryBuilder->andWhere("{$field} LIKE " . $queryBuilder->createNamedParameter($value));
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $cond
     */
    protected function parseInCondition(QueryBuilder $queryBuilder, $cond)
    {
        if ($cond[1]) {
            $isNumeric = $cond[2];
            $sql = '';
            /** @var array $values */
            $values = $cond[1];
            foreach ($values as $v) {
                $sql .= ($isNumeric ? (float)$v : $queryBuilder->createNamedParameter($v)) . ',';
            }
            $field = $this->mapField($cond[0]);
            $queryBuilder->andWhere("{$field} IN (" . \rtrim($sql, ',') . ')');
        } else {
            $queryBuilder->andWhere('1 = 2'); // IN empty array is always false
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $cond
     */
    protected function parseBetweenCondition(QueryBuilder $queryBuilder, $cond)
    {
        $field = $this->mapField($cond[0]);
        $queryBuilder->andWhere("{$field} BETWEEN " . $queryBuilder->createNamedParameter($cond[1])
            . ' AND ' . $queryBuilder->createNamedParameter($cond[2]));
    }

    /**
     * @param string $exp
     * @param QueryBuilder $queryBuilder
     * @param $cond
     */
    protected function parseSimpleCondition(string $exp, QueryBuilder $queryBuilder, $cond)
    {
        $field = $this->mapField($cond[0]);
        $queryBuilder->andWhere("{$field} $exp " . $queryBuilder->createNamedParameter($cond[1]));
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    protected function mapField($field)
    {
        return $this->map[$field] ?? $field;
    }
}
