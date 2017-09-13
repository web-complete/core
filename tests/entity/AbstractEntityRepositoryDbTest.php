<?php

use Doctrine\DBAL\DriverManager;
use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\entity\AbstractEntityEntityRepositoryDb;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\utils\hydrator\Hydrator;

class AbstractEntityRepositoryDbTest extends \PHPUnit\Framework\TestCase
{

    public function testTransaction()
    {
        $closure = function(){};
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('transactional')->with($closure);
        $rep = $this->createRep(null, null, null, $conn);
        $rep->transaction($closure);
    }

    public function testFindById()
    {
        $stmt = $this->createMock(\Doctrine\DBAL\Statement::class);
        $stmt->method('fetch')->willReturn([['a' => 1]]);

        $qb = $this->createMock(\Doctrine\DBAL\Query\QueryBuilder::class);
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->expects($this->once())->method('execute')->willReturn($stmt);

        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);

        $of = $this->createMock(ObjectFactory::class);
        $of->method('createFromData')->willReturn(2);

        $rep = $this->createRep($of, null, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals(2, $rep->findById(1));
    }

    public function testFindOne()
    {
        $stmt = $this->createMock(\Doctrine\DBAL\Statement::class);
        $stmt->method('fetch')->willReturn(['a' => 1, 'arr' => '[1,2,3]', 'arr2' => null]);

        $qb = $this->createMock(\Doctrine\DBAL\Query\QueryBuilder::class);
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->expects($this->once())->method('execute')->willReturn($stmt);

        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);

        $of = $this->createMock(ObjectFactory::class);
        $of->method('createFromData')->with(['a' => 1, 'arr' => [1,2,3], 'arr2' => null])->willReturn(2);

        $rep = $this->createRep($of, null, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals(2, $rep->findOne(new Condition()));
    }

    public function testFindAll()
    {
        $stmt = $this->createMock(\Doctrine\DBAL\Statement::class);
        $stmt->method('fetchAll')->willReturn([['a' => 1], ['a' => 2]]);

        $qb = $this->createMock(\Doctrine\DBAL\Query\QueryBuilder::class);
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->expects($this->once())->method('execute')->willReturn($stmt);

        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);

        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setId(11);
        $o1->a = 1;

        $o2 = new AbstractEntityRepositoryDbTestEntity();
        $o2->setId(12);
        $o2->a = 2;

        $of = $this->createMock(ObjectFactory::class);
        $of->method('createFromData')->willReturn($o1, $o2);

        $rep = $this->createRep($of, null, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals([11 => $o1,12 => $o2], $rep->findAll(new Condition()));
    }

    public function testCount()
    {
        $stmt = $this->createMock(\Doctrine\DBAL\Statement::class);
        $stmt->method('rowCount')->willReturn(3);

        $qb = $this->createMock(\Doctrine\DBAL\Query\QueryBuilder::class);
        $qb->method('select')->willReturnSelf();
        $qb->method('where')->willReturnSelf();
        $qb->method('setParameter')->willReturnSelf();
        $qb->expects($this->once())->method('execute')->willReturn($stmt);

        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);

        $rep = $this->createRep(null, null, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals(3, $rep->count(new Condition()));
    }

    public function testSaveNew()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->a = 1;
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('insert')->with('tbl', ['a' => 1, 'arr' => null, 'arr2' => null]);
        $conn->expects($this->once())->method('lastInsertId')->willReturn(22);
        $rep = $this->createRep(null, null, null, $conn);
        $rep->save($o1);
        $this->assertEquals(22, $o1->getId());
    }

    public function testSaveUpdate()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setId(33);
        $o1->a = 2;
        $o1->arr = [1,2,3];
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('update')->with('tbl', ['id' => 33, 'a' => 2, 'arr' => '[1,2,3]', 'arr2' => null], ['id' => 33]);
        $rep = $this->createRep(null, null, null, $conn);
        $rep->save($o1);
        $this->assertEquals(33, $o1->getId());
    }

    public function testDelete()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setId(44);
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('delete')->with('tbl', ['id' => 44]);
        $rep = $this->createRep(null, null, null, $conn);
        $rep->delete($o1->getId());
    }

    public function testSelectQuery()
    {
        $rep = $this->createRep();
        $class = new ReflectionClass($rep);
        $method = $class->getMethod('selectQuery');
        $method->setAccessible(true);
        /** @var \Doctrine\DBAL\Query\QueryBuilder $qb */
        $qb = $method->invokeArgs($rep, [new Condition(['a' => 1])]);
        $sql = $qb->getSQL();
        $this->assertEquals('SELECT t1.* FROM tbl t1 WHERE a = :dcValue1', $sql);
    }

    /**
     * @param null $of
     * @param null $h
     * @param null $p
     * @param null $c
     * @param array $mockedMethods
     * @return PHPUnit_Framework_MockObject_MockObject|AbstractEntityEntityRepositoryDb
     */
    protected  function createRep($of = null, $h = null, $p = null, $c = null, $mockedMethods = [])
    {
        $of = $of ?: $this->createMock(ObjectFactory::class);
        $of->expects($this->any())->method('create')->willReturn(new AbstractEntityRepositoryDbTestEntity());

        $hydrator = $h ?: new Hydrator();
        $parser = $p ?: new ConditionDbParser();
        $conn = $c ?: DriverManager::getConnection(['url' => 'sqlite:///:memory:']);

        $aer = $this->getMockForAbstractClass(AbstractEntityEntityRepositoryDb::class, [$of, $hydrator, $parser, $conn], '', true, true, true, $mockedMethods);
        $reflection = new ReflectionClass($aer);
        $reflection_property = $reflection->getProperty('table');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, 'tbl');
        $reflection_property = $reflection->getProperty('serializeFields');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, ['arr', 'arr2']);
        $reflection_property = $reflection->getProperty('serializeStrategy');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, AbstractEntityEntityRepositoryDb::SERIALIZE_STRATEGY_JSON);
        return $aer;
    }

}

class AbstractEntityRepositoryDbTestEntity extends \WebComplete\core\entity\AbstractEntity {
    public $a;
    public $arr;
    public $arr2;
}