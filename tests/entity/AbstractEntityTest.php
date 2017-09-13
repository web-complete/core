<?php

class AbstractEntityTest extends \PHPUnit\Framework\TestCase
{

    public function testSetGetId()
    {
        /** @var \WebComplete\core\entity\AbstractEntity $entity */
        $entity = $this->createTestProxy(\WebComplete\core\entity\AbstractEntity::class);
        $this->assertNull($entity->getId());
        $entity->setId('SOME_XML_ID');
        $this->assertEquals('SOME_XML_ID', $entity->getId());
    }

}
