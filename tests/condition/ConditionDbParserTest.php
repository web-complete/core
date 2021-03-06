<?php

use WebComplete\core\condition\Condition;

class ConditionDbParserTest extends \PHPUnit\Framework\TestCase
{

    public function testParseEmpty()
    {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $qb */
        $qb = $this->createMock(\Doctrine\DBAL\Query\QueryBuilder::class);
        $parser = new \WebComplete\core\condition\ConditionDbParser();
        $parser->parse($qb);
        $this->assertTrue(true);
    }

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
        $condition->addLikeCondition('l', 'asd');
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
        $parser->parse($qb, $condition, [
            'A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd', 'E' => 'e', 'F' => 'f', 'G' => 'g', 'L' => 'l',
            'H' => 'h', 'I' => 'i', 'J' => 'j', 'F1' => 'f1', 'F2' => 'f2'
        ]);

        $sql = 'SELECT * FROM tmp WHERE (A = :dcValue1) AND (B <> :dcValue2) AND (C < :dcValue3) AND (D > :dcValue4) AND (E <= :dcValue5) AND (F >= :dcValue6) AND (G BETWEEN :dcValue7 AND :dcValue8) AND (L LIKE :dcValue9) AND (H IN (:dcValue10,:dcValue11)) AND (I IN (9,10)) AND (1 = 2) ORDER BY F2 desc LIMIT 15 OFFSET 5';
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
            'dcValue9' => '%asd%',
            'dcValue10' => 9,
            'dcValue11' => 10,

        ], $qb->getParameters());
    }

}