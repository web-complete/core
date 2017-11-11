<?php

use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionMicroDbParser;
use WebComplete\microDb\MicroDb;

/**
 * integration test
 */
class ConditionMicroDbParserTest extends CoreTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->clearDir();
    }

    public function tearDown()
    {
        $this->clearDir();
        parent::tearDown();
    }

    public function testEquals()
    {
        $condition = new Condition();
        $condition->addEqualsCondition('flag', 3);
        $this->assertEquals([
            ['id' => 3, 'name' => 'user 3', 'flag' => 3],
        ], $this->getFilteredItems($condition));
    }

    public function testNotEquals()
    {
        $condition = new Condition();
        $condition->addNotEqualsCondition('flag', 3);
        $this->assertEquals([
            ['id' => 1, 'name' => 'user 1', 'flag' => 1],
            ['id' => 2, 'name' => 'user 2', 'flag' => 2],
            ['id' => 4, 'name' => 'user 4', 'flag' => 4],
            ['id' => 5, 'name' => 'user 5', 'flag' => 5],
        ], $this->getFilteredItems($condition));
    }

    public function testLessThan()
    {
        $condition = new Condition();
        $condition->addLessThanCondition('flag', 2);
        $this->assertEquals([
            ['id' => 1, 'name' => 'user 1', 'flag' => 1],
        ], $this->getFilteredItems($condition));
    }

    public function testGreaterThan()
    {
        $condition = new Condition();
        $condition->addGreaterThanCondition('flag', 4);
        $this->assertEquals([
            ['id' => 5, 'name' => 'user 5', 'flag' => 5],
        ], $this->getFilteredItems($condition));
    }

    public function testLessOrEquals()
    {
        $condition = new Condition();
        $condition->addLessOrEqualsCondition('flag', 2);
        $this->assertEquals([
            ['id' => 1, 'name' => 'user 1', 'flag' => 1],
            ['id' => 2, 'name' => 'user 2', 'flag' => 2],
        ], $this->getFilteredItems($condition));
    }

    public function testGreaterOrEquals()
    {
        $condition = new Condition();
        $condition->addGreaterOrEqualsCondition('flag', 4);
        $this->assertEquals([
            ['id' => 4, 'name' => 'user 4', 'flag' => 4],
            ['id' => 5, 'name' => 'user 5', 'flag' => 5],
        ], $this->getFilteredItems($condition));
    }

    public function testBetween()
    {
        $condition = new Condition();
        $condition->addBetweenCondition('flag', 2, 4);
        $this->assertEquals([
            ['id' => 2, 'name' => 'user 2', 'flag' => 2],
            ['id' => 3, 'name' => 'user 3', 'flag' => 3],
            ['id' => 4, 'name' => 'user 4', 'flag' => 4],
        ], $this->getFilteredItems($condition));
    }

    public function testLike1()
    {
        $condition = new Condition();
        $condition->addLikeCondition('name', 'user 2');
        $this->assertEquals([
            ['id' => 2, 'name' => 'user 2', 'flag' => 2],
        ], $this->getFilteredItems($condition));
    }

    public function testLike2()
    {
        $condition = new Condition();
        $condition->addLikeCondition('name', 'user ');
        $this->assertEquals([
            ['id' => 1, 'name' => 'user 1', 'flag' => 1],
            ['id' => 2, 'name' => 'user 2', 'flag' => 2],
            ['id' => 3, 'name' => 'user 3', 'flag' => 3],
            ['id' => 4, 'name' => 'user 4', 'flag' => 4],
            ['id' => 5, 'name' => 'user 5', 'flag' => 5],
        ], $this->getFilteredItems($condition));
    }

    public function testIn()
    {
        $condition = new Condition();
        $condition->addInCondition('flag', [1, 3, 5]);
        $this->assertEquals([
            ['id' => 1, 'name' => 'user 1', 'flag' => 1],
            ['id' => 3, 'name' => 'user 3', 'flag' => 3],
            ['id' => 5, 'name' => 'user 5', 'flag' => 5],
        ], $this->getFilteredItems($condition));
    }

    protected function getFilteredItems(Condition $condition): array
    {
        $conditionParser = new ConditionMicroDbParser();
        $microDb = new MicroDb(__DIR__ . '/storage', 'test-db');
        $collection = $microDb->getCollection('users');
        $this->assertCount(0, $collection->fetchAll());
        $collection->insertBatch([
            ['name' => 'user 1', 'flag' => 1],
            ['name' => 'user 2', 'flag' => 2],
            ['name' => 'user 3', 'flag' => 3],
            ['name' => 'user 4', 'flag' => 4],
            ['name' => 'user 5', 'flag' => 5],
        ]);
        return $collection->fetchAll(
            $conditionParser->filter($condition, $limit, $offset),
            $conditionParser->sort($condition),
            $limit,
            $offset
        );
    }

    protected function clearDir()
    {
        @\unlink(__DIR__ . '/storage/test-db_users.fdb');
        @\rmdir(__DIR__ . '/storage');
    }
}