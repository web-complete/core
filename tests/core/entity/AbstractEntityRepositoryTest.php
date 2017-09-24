<?php

use WebComplete\core\entity\AbstractEntity;
use WebComplete\core\entity\AbstractEntityRepository;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\utils\hydrator\Hydrator;

class AbstractEntityRepositoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreate()
    {
        $of = $this->createMock(ObjectFactory::class);
        $of->expects($this->once())->method('create')
            ->willReturn($this->createMock(AbstractEntity::class));
        $hydrator = new Hydrator();
        $aer = $this->getMockForAbstractClass(AbstractEntityRepository::class, [$of, $hydrator]);
        /** @var AbstractEntityRepository $aer */
        $aer->create();
    }

}