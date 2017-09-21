<?php

namespace WebComplete\core\condition;

use Doctrine\DBAL\Query\QueryBuilder;


class ConditionDbParser
{

    public function parse(QueryBuilder $queryBuilder, Condition $condition = null)
    {
        if(!$condition) {
            return;
        }
        foreach ($condition->getConditions() as $cond) {
            if(is_array($cond)) {
                $cond = array_values($cond);
                $type = array_shift($cond);
                switch ($type) {
                    case Condition::EQUALS:
                        $queryBuilder->andWhere("{$cond[0]} = " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::NOT_EQUALS:
                        $queryBuilder->andWhere("{$cond[0]} <> " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::LESS_THAN:
                        $queryBuilder->andWhere("{$cond[0]} < " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::GREATER_THAN:
                        $queryBuilder->andWhere("{$cond[0]} > " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::LESS_OR_EQUALS:
                        $queryBuilder->andWhere("{$cond[0]} <= " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::GREATER_OR_EQUALS:
                        $queryBuilder->andWhere("{$cond[0]} >= " . $queryBuilder->createNamedParameter($cond[1]));
                        break;
                    case Condition::BETWEEN:
                        $queryBuilder->andWhere("{$cond[0]} BETWEEN " . $queryBuilder->createNamedParameter($cond[1])
                            . " AND " . $queryBuilder->createNamedParameter($cond[2]));
                        break;
                    case Condition::LIKE:
                        $value = $cond[2] ? '%' : '';
                        $value .= $cond[1];
                        $value .= $cond[3] ? '%' : '';
                        $queryBuilder->andWhere("{$cond[0]} LIKE " . $queryBuilder->createNamedParameter($value));
                        break;
                    case Condition::IN:
                        if($cond[1]) {
                            $isNumeric = $cond[2];
                            $sql = '';
                            foreach ($cond[1] as $v) {
                                $sql .= ($isNumeric ? (float)$v : $queryBuilder->createNamedParameter($v)) . ',';
                            }
                            $queryBuilder->andWhere("{$cond[0]} IN (" . rtrim($sql, ',') . ")");
                        }
                        else {
                            $queryBuilder->andWhere('1 = 2'); // IN empty array is always false
                        }
                        break;
                }
            }
        }

        foreach ($condition->getSort() as $field => $direction) {
            $queryBuilder->orderBy($field, $direction == SORT_DESC ? 'desc' : 'asc');
        }

        if($offset = $condition->getOffset()) {
            $queryBuilder->setFirstResult($offset);
        }

        if($limit = $condition->getLimit()) {
            $queryBuilder->setMaxResults($limit);
        }
    }

}