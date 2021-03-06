<?php

use Doctrine\DBAL\DriverManager;
use Mvkasatkin\mocker\Mocker;
use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\entity\AbstractEntityRepositoryDb;
use WebComplete\core\factory\EntityFactory;
use WebComplete\core\utils\typecast\Cast;

class AbstractEntityRepositoryDbTest extends CoreTestCase
{

    public function testTransaction()
    {
        $closure = function(){};
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('transactional')->with($closure);
        $rep = $this->createRep(null, null, $conn);
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

        $entity = Mocker::create(AbstractEntity::class, [
            Mocker::method('mapFromArray', 1)
        ]);
        $of = $this->createMock(EntityFactory::class);
        $of->method('create')->willReturn($entity);

        $rep = $this->createRep($of, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals($entity, $rep->findById(1));
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

        $entity = Mocker::create(AbstractEntity::class, [
            Mocker::method('mapFromArray', 1)
        ]);
        $of = $this->createMock(EntityFactory::class);
        $of->method('create')->willReturn($entity);

        $rep = $this->createRep($of, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals($entity, $rep->findOne(new Condition()));
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

        $of = $this->createMock(EntityFactory::class);
        $of->method('create')->willReturn($o1, $o2);

        $rep = $this->createRep($of, null, $conn, ['selectQuery']);
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

        $rep = $this->createRep(null, null, $conn, ['selectQuery']);
        $rep->expects($this->once())->method('selectQuery')->willReturn($qb);
        $this->assertEquals(3, $rep->count(new Condition()));
    }

    public function testSaveNew()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setA(1);
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('insert')->with('tbl', ['a' => 1, 'arr' => null, 'arr2' => null]);
        $conn->expects($this->once())->method('lastInsertId')->willReturn(22);
        $rep = $this->createRep(null, null, $conn);
        $rep->save($o1);
        $this->assertEquals(22, $o1->getId());
    }

    public function testSaveUpdate()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setId(33);
        $o1->setA(2);
        $o1->setArr([1,2,3]);
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('update')->with('tbl', ['id' => 33, 'a' => 2, 'arr' => '[1,2,3]', 'arr2' => null], ['t1.id' => 33]);
        $rep = $this->createRep(null, null, $conn);
        $rep->save($o1);
        $this->assertEquals(33, $o1->getId());
    }

    public function testDelete()
    {
        $o1 = new AbstractEntityRepositoryDbTestEntity();
        $o1->setId(44);
        $conn = $this->createMock(\Doctrine\DBAL\Connection::class);
        $conn->expects($this->once())->method('delete')->with('tbl', ['t1.id' => 44]);
        $rep = $this->createRep(null, null, $conn);
        $rep->delete($o1->getId());
    }

    public function testDeleteAll()
    {
        $query = Mocker::create(\Doctrine\DBAL\Query\QueryBuilder::class, [
            Mocker::method('delete', 1, [null, 't1']),
            Mocker::method('execute', 1),
        ]);
        /** @var AbstractEntityRepositoryDb $aer */
        $aer = Mocker::create(AbstractEntityRepositoryDb::class, [
            Mocker::method('selectQuery', 1, [null])->returns($query)
        ]);
        $aer->deleteAll();
    }

    public function testGetMap()
    {
        $query = Mocker::create(\Doctrine\DBAL\Query\QueryBuilder::class, [
            Mocker::method('select', 1, [['t1.some', 't1.id']]),
            Mocker::method('execute', 1)->returnsSelf(),
            Mocker::method('fetchAll', 1)->returns([
                ['id' => 11, 'some' => 'aaa'],
                ['id' => 12, 'some' => 'bbb'],
            ]),
        ]);
        /** @var AbstractEntityRepositoryDb $aer */
        $aer = Mocker::create(AbstractEntityRepositoryDb::class, [
            Mocker::method('selectQuery', 1, [null])->returns($query)
        ]);
        $map = $aer->getMap('some');
        $this->assertEquals([
            11 => 'aaa',
            12 => 'bbb'
        ], $map);
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

    public function testGetTable()
    {
        $aer = $this->createRep();
        $this->assertEquals('tbl', $aer->getTable());
    }

    /**
     * @param null $of
     * @param null $h
     * @param null $p
     * @param null $c
     * @param array $mockedMethods
     *
     * @return PHPUnit_Framework_MockObject_MockObject|AbstractEntityRepositoryDb
     */
    protected  function createRep($of = null, $p = null, $c = null, $mockedMethods = [])
    {
        $of = $of ?: $this->createMock(EntityFactory::class);
        $of->expects($this->any())->method('create')->willReturn(new AbstractEntityRepositoryDbTestEntity());

        $parser = $p ?: new ConditionDbParser();
        $conn = $c ?: DriverManager::getConnection(['url' => 'sqlite:///:memory:']);

        $aer = $this->getMockForAbstractClass(AbstractEntityRepositoryDb::class, [$of, $parser, $conn], '', true, true, true, $mockedMethods);
        $reflection = new ReflectionClass($aer);
        $reflection_property = $reflection->getProperty('table');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, 'tbl');
        $reflection_property = $reflection->getProperty('serializeFields');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, ['arr', 'arr2']);
        $reflection_property = $reflection->getProperty('serializeStrategy');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($aer, AbstractEntityRepositoryDb::SERIALIZE_STRATEGY_JSON);
        return $aer;
    }

}

class AbstractEntityRepositoryDbTestEntity extends AbstractEntity {

    /**
     * @return array
     */
    public static function fields(): array
    {
        return [
            'a' => Cast::STRING,
            'arr' => Cast::ARRAY,
            'arr2' => Cast::ARRAY,
        ];
    }

    public function setA($value)
    {
        $this->set('a', $value);
    }

    public function setArr($value)
    {
        $this->set('arr', $value);
    }
}
