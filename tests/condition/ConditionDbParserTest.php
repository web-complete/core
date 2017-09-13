<?php

use WebComplete\core\condition\Condition;

class ConditionDbParserTest extends \PHPUnit\Framework\TestCase
{

    public function testParse()
    {
        $condition = new Condition();
        $condition->addEqualsCondition('a', 1);
        $condition->addNotEqualsCondition('b', 2);
        $condition->addLessThanCondition('c', 3);
        $condition->addGreaterThanCondition('d', 4);
        $condition->addLessOrEqualsCondition('e', 5);
        $condition->addGreaterOrEqualsCondition('f', 6);
        $condition->addBetweenCondition('g', 7, 8);
        $condition->addInCondition('h', [9, 10]);
        $condition->addInCondition('i', [9, 10], true);
        $condition->addInCondition('j', []);
        $condition->addSort('f1', SORT_ASC);
        $condition->addSort('f2', SORT_DESC);
        $condition->offset(5);
        $condition->limit(15);

        $parser = new \WebComplete\core\condition\ConditionDbParser();

        $conn = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
        $qb = new \Doctrine\DBAL\Query\QueryBuilder($conn);
        $qb->select('*')->from('tmp');
        $parser->parse($qb, $condition);

        $sql = 'SELECT * FROM tmp WHERE (a = :dcValue1) AND (b <> :dcValue2) AND (c < :dcValue3) AND (d > :dcValue4) AND (e <= :dcValue5) AND (f >= :dcValue6) AND (g BETWEEN :dcValue7 AND :dcValue8) AND (h IN (:dcValue9,:dcValue10)) AND (i IN (9,10)) AND (1 = 2) ORDER BY f2 desc LIMIT 15 OFFSET 5';
        $this->assertEquals($sql, $qb->getSQL());
        $this->assertEquals([
            'dcValue1' => 1,
            'dcValue2' => 2,
            'dcValue3' => 3,
            'dcValue4' => 4,
            'dcValue5' => 5,
            'dcValue6' => 6,
            'dcValue7' => 7,
            'dcValue8' => 8,
            'dcValue9' => 9,
            'dcValue10' => 10,

        ], $qb->getParameters());
    }

}