<?php

use WebComplete\core\entity\AbstractEntityRepository;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\hydrator\Hydrator;

class AbstractEntityRepositoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreate()
    {
        $of = $this->createMock(ObjectFactory::class);
        $of->expects($this->once())->method('create');
        $hydrator = new Hydrator();
        $aer = $this->getMockForAbstractClass(AbstractEntityRepository::class, [$of, $hydrator]);
        /** @var AbstractEntityRepository $aer */
        $aer->create();
    }

}