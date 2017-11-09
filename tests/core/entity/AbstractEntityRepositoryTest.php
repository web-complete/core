<?php

use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\entity\AbstractEntityRepository;
use WebComplete\core\factory\ObjectFactory;

class AbstractEntityRepositoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreate()
    {
        $of = $this->createMock(ObjectFactory::class);
        $of->expects($this->once())->method('create')
            ->willReturn($this->createMock(AbstractEntity::class));
        $aer = $this->getMockForAbstractClass(AbstractEntityRepository::class, [$of]);
        /** @var AbstractEntityRepository $aer */
        $aer->create();
    }

    public function testCreateFromData()
    {
        $data = [1,2,3];

        $of = $this->createMock(ObjectFactory::class);
        $of->expects($this->once())->method('create')
            ->willReturn(new Entity2());
        $aer = $this->getMockForAbstractClass(AbstractEntityRepository::class, [$of]);
        /** @var AbstractEntityRepository $aer */
        $aer->createFromData($data);
    }
}

class Entity2 extends AbstractEntity {

    /**
     * @return array
     */
    public static function fields(): array
    {
        return [];
    }
}
