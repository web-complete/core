<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\entity\AbstractEntityRepositoryDb;
use WebComplete\core\entity\AbstractEntityService;
use WebComplete\core\utils\paginator\Paginator;

class AbstractEntityServiceTest extends CoreTestCase
{

    public function testProxyTransaction()
    {
        $this->createAService(['transaction'])->transaction(function(){});
    }

    public function testProxyCreate()
    {
        $this->createAService(['create'])->create();
    }

    public function testProxyCreateFromData()
    {
        $this->createAService(['createFromData'])->createFromData([]);
    }

    public function testProxyFindById()
    {
        $this->createAService(['findById'])->findById(1);
    }

    public function testProxyFindOne()
    {
        $this->createAService(['findOne'])->findOne(new Condition());
    }

    public function testProxyFindAll()
    {
        $this->createAService(['findAll'])->findAll(new Condition());
    }

    public function testProxyCount()
    {
        $this->createAService(['count'])->count(new Condition());
    }

    public function testProxySave()
    {
        /** @var AbstractEntity $e */
        $e = $this->createMock(AbstractEntity::class);
        $this->createAService(['save'])->save($e);
    }

    public function testProxyDelete()
    {
        $this->createAService(['delete'])->delete(1);
    }

    public function testProxyDeleteAll()
    {
        $this->createAService(['deleteAll'])->deleteAll();
    }

    public function testProxyGetMap()
    {
        $this->createAService(['getMap'])->getMap('id');
    }

    public function testProxyCreateCondition()
    {
        $this->createAService(['createCondition'])->createCondition();
    }

    public function testList()
    {
        /** @var Condition $condition */
        $condition = Mocker::create(Condition::class, [
            Mocker::method('limit', 1, 25),
            Mocker::method('offset', 1, 10),
        ]);
        /** @var Paginator $paginator */
        $paginator = Mocker::create(Paginator::class, [
            Mocker::method('getTotal', 1)->returns(0),
            Mocker::method('getLimit', 1)->returns(25),
            Mocker::method('getOffset', 1)->returns(10),
        ]);
        /** @var AbstractEntityService $service */
        $service = Mocker::create(AbstractEntityService::class, [
            Mocker::method('count', 1, $condition)->returns(100),
            Mocker::method('findAll', 1, $condition)->returns([]),
        ]);
        $service->list($paginator, $condition);
    }

    public function testListEmpty()
    {
        /** @var Condition $condition */
        $condition = Mocker::create(Condition::class);
        /** @var Paginator $paginator */
        $paginator = Mocker::create(Paginator::class, [
            Mocker::method('getTotal', 1)->returns(0),
        ]);
        /** @var AbstractEntityService $service */
        $service = Mocker::create(AbstractEntityService::class, [
            Mocker::method('count', 1, $condition)->returns(0),
        ]);
        $this->assertEquals([], $service->list($paginator, $condition));
    }

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|AbstractEntityService
     */
    protected function createAService($methods = [])
    {
        $of = $this->createMock(\WebComplete\core\factory\EntityFactory::class);
        $parser = new ConditionDbParser();
        $conn = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'sqlite:///:memory:']);

        $aer = $this->getMockForAbstractClass(
            AbstractEntityRepositoryDb::class,
            [$of, $parser, $conn], '', true,
            true, true, $methods);

        foreach ($methods as $method) {
            $aer->expects($this->once())->method($method);
        }

        return $this->getMockForAbstractClass(AbstractEntityService::class, [$aer]);
    }
}
