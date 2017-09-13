<?php

use WebComplete\core\condition\Condition;
use WebComplete\core\container\ContainerInterface;
use WebComplete\core\factory\ConditionFactory;

class ConditionFactoryTest extends \PHPUnit\Framework\TestCase
{

    public function testInstance()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('make')->with(Condition::class)->willReturn('yes');
        /** @var ContainerInterface $container */
        $this->assertEquals('yes', (new ConditionFactory($container))->create());
    }

}
