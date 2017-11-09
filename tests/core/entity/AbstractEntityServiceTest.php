<?php

use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionDbParser;
use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\entity\AbstractEntityRepositoryDb;
use WebComplete\core\entity\AbstractEntityRepository;
use WebComplete\core\entity\AbstractEntityService;
use WebComplete\core\utils\hydrator\Hydrator;

class AbstractEntityServiceTest extends \PHPUnit\Framework\TestCase
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
        $this->createAService(['createFromData'])->createFromData([], []);
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

    /**
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|AbstractEntityService
     */
    protected function createAService($methods = [])
    {
        $of = $this->createMock(\WebComplete\core\factory\ObjectFactory::class);
        $hydrator = new Hydrator();
        $parser = new ConditionDbParser();
        $conn = \Doctrine\DBAL\DriverManager::getConnection(['url' => 'sqlite:///:memory:']);

        $aer = $this->getMockForAbstractClass(
            AbstractEntityRepositoryDb::class,
            [$of, $hydrator, $parser, $conn], '', true,
            true, true, $methods);

        foreach ($methods as $method) {
            $aer->expects($this->once())->method($method);
        }

        return $this->getMockForAbstractClass(AbstractEntityService::class, [$aer]);
    }

}