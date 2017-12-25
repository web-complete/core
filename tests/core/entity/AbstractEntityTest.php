<?php

class AbstractEntityTest extends \PHPUnit\Framework\TestCase
{

    public function testSetGetId()
    {
        $entity = new Entity1();
        $this->assertNull($entity->getId());
        $entity->setId('SOME_XML_ID');
        $this->assertEquals('SOME_XML_ID', $entity->getId());
    }

    public function testMagic()
    {
        $entity = new Entity1();
        $this->assertTrue($entity->has('some'));
        $this->assertFalse($entity->has('some2'));
        $entity->set('some', '123');
        $this->assertSame(123.0, $entity->get('some'));
        $this->assertSame(123.0, $entity->some);
        $this->assertSame(null, $entity->get('some2'));
        $this->assertSame(null, $entity->some2);
        $this->assertTrue(isset($entity->some));
        $this->assertFalse(isset($entity->some2));
    }

}

class Entity1 extends \WebComplete\core\entity\AbstractEntity {

    /**
     * @return array
     */
    public static function fields(): array
    {
        return [
            'some' => \WebComplete\core\utils\typecast\Cast::FLOAT
        ];
    }
}
