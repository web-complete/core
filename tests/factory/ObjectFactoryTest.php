<?php

use WebComplete\core\container\ContainerInterface;
use WebComplete\core\factory\ObjectFactory;
use WebComplete\core\hydrator\Hydrator;
use WebComplete\core\hydrator\HydratorInterface;

class ObjectFactoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreate()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('make')->with('SomeClass')->willReturn(new stdClass());
        $hydrator = $this->getMockForAbstractClass(HydratorInterface::class);
        /** @var ContainerInterface $container */
        /** @var HydratorInterface $hydrator */
        $objectFactory = new ObjectFactory($container, $hydrator, 'SomeClass');
        $object = $objectFactory->create();
        $this->assertInstanceOf(stdClass::class, $object);
    }

    public function testCreateFromData()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('make')->with(ObjectFactoryTest1::class)->willReturn(new ObjectFactoryTest1);
        $hydrator = new Hydrator();
        /** @var ContainerInterface $container */
        $objectFactory = new ObjectFactory($container, $hydrator, ObjectFactoryTest1::class);
        $object = $objectFactory->createFromData(['a1' => 'v1', 'a2' => 'v2']);
        $this->assertInstanceOf(ObjectFactoryTest1::class, $object);
        $this->assertEquals('v1', $object->a1);
        $this->assertEquals('v2', $object->a2);
    }

}

class ObjectFactoryTest1
{
    public $a1;
    public $a2;
}