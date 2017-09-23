<?php

namespace WebComplete\core\utils\alias;

class AliasServiceTest extends \CoreTestCase
{

    public function testInstance()
    {
        $a = new AliasService();
        $this->assertInstanceOf(AliasService::class, $a);
    }

    public function testSetInConstructor()
    {
        $a = new AliasService(['@a' => 'b']);
        $this->assertEquals(['@a' => 'b'], $a->getAliases());
    }

    public function testSetGetAlias()
    {
        $a = new AliasService();
        $a->setAlias('@a', 'c');
        $this->assertEquals(['@a' => 'c'], $a->getAliases());
    }

    public function testSetGetAliasException()
    {
        $this->expectException(AliasException::class);
        $a = new AliasService();
        $a->setAlias('a', 'c');
    }

    public function testGetSimple()
    {
        $a = new AliasService();
        $a->setAlias('@alias1', 'value1');
        $this->assertEquals('value1', $a->get('@alias1'));
    }

    public function testGetSubstring()
    {
        $a = new AliasService();
        $a->setAlias('@alias2', 'value2');
        $this->assertEquals('value2/value3/4', $a->get('@alias2/value3/4'));
    }

    public function testGetNotAlias()
    {
        $a = new AliasService();
        $this->assertEquals('value1', $a->get('value1'));
    }

    public function testNoAliasException()
    {
        $this->expectException(AliasException::class);
        $a = new AliasService();
        $this->assertEquals('value1', $a->get('@alias1'));
    }

    public function testNoAliasNoException()
    {
        $a = new AliasService();
        $this->assertEquals(null, $a->get('@alias1', false));
    }

}
