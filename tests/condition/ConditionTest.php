<?php

use WebComplete\core\condition\Condition;

class ConditionTest extends \PHPUnit\Framework\TestCase
{

    public function testConditions()
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
        $condition->addLikeCondition('i', 'asd', true, false);
        $condition->addSort('f1', SORT_ASC);
        $condition->addSort('f2', SORT_DESC);
        $condition->offset(5);
        $condition->limit(15);

        $this->assertEquals(5, $condition->getOffset());
        $this->assertEquals(15, $condition->getLimit());
        $this->assertEquals(['f1' => SORT_ASC, 'f2' => SORT_DESC], $condition->getSort());
        $this->assertEquals([
            [Condition::EQUALS, 'a', 1],
            [Condition::NOT_EQUALS, 'b', 2],
            [Condition::LESS_THAN, 'c', 3],
            [Condition::GREATER_THAN, 'd', 4],
            [Condition::LESS_OR_EQUALS, 'e', 5],
            [Condition::GREATER_OR_EQUALS, 'f', 6],
            [Condition::BETWEEN, 'g', 7, 8],
            [Condition::IN, 'h', [9, 10], false],
            [Condition::LIKE, 'i', 'asd', true, false],
        ], $condition->getConditions());
    }

    public function testConditionsArgs()
    {
        $condition = new Condition([
            [Condition::EQUALS, 'a', 1],
            [Condition::NOT_EQUALS, 'b', 2],
            [Condition::LESS_THAN, 'c', 3],
            [Condition::GREATER_THAN, 'd', 4],
            [Condition::LESS_OR_EQUALS, 'e', 5],
            [Condition::GREATER_OR_EQUALS, 'f', 6],
            [Condition::BETWEEN, 'g', 7, 8],
            [Condition::IN, 'h', [9, 10]],
            [Condition::LIKE, 'i', 'asd', true, false],
        ], 'f1', SORT_DESC, 10, 5);

        $this->assertEquals(10, $condition->getOffset());
        $this->assertEquals(5, $condition->getLimit());
        $this->assertEquals(['f1' => SORT_DESC], $condition->getSort());
        $this->assertEquals([
            [Condition::EQUALS, 'a', 1],
            [Condition::NOT_EQUALS, 'b', 2],
            [Condition::LESS_THAN, 'c', 3],
            [Condition::GREATER_THAN, 'd', 4],
            [Condition::LESS_OR_EQUALS, 'e', 5],
            [Condition::GREATER_OR_EQUALS, 'f', 6],
            [Condition::BETWEEN, 'g', 7, 8],
            [Condition::IN, 'h', [9, 10]],
            [Condition::LIKE, 'i', 'asd', true, false],
        ], $condition->getConditions());
    }

    public function testConditionsArgsShort()
    {
        $condition = new Condition([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => ['a','b','c'],
            [Condition::BETWEEN, 'g', 7, 8],
            [Condition::IN, 'h', [9, 10]],
        ], 'f1', SORT_DESC, 10, 5);

        $this->assertEquals(10, $condition->getOffset());
        $this->assertEquals(5, $condition->getLimit());
        $this->assertEquals(['f1' => SORT_DESC], $condition->getSort());
        $this->assertEquals([
            [Condition::EQUALS, 'a', 1],
            [Condition::EQUALS, 'b', 2],
            [Condition::EQUALS, 'c', 3],
            [Condition::IN, 'd', ['a', 'b', 'c']],
            [Condition::BETWEEN, 'g', 7, 8],
            [Condition::IN, 'h', [9, 10]],
        ], $condition->getConditions());
    }

}
