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

}

class Entity1 extends \WebComplete\core\entity\AbstractEntity {

    /**
     * @return array
     */
    public static function fields(): array
    {
        return [];
    }
}
