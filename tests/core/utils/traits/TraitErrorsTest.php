<?php

class TraitErrorsTest extends \PHPUnit\Framework\TestCase
{

    public function test()
    {
        $o = new TraitErrorsTestC();
        $this->assertFalse($o->hasErrors());

        $o->addError('a', 'a1');
        $o->addError('a', 'a2');
        $o->addError('b', 'b1');
        $this->assertTrue($o->hasErrors());
        $this->assertTrue($o->hasErrors('a'));
        $this->assertTrue($o->hasErrors('b'));
        $this->assertFalse($o->hasErrors('c'));

        $this->assertEquals([
            'a' => ['a1', 'a2'],
            'b' => ['b1']
        ],$o->getErrors());

        $this->assertEquals(['b1'], $o->getErrors('b'));

        $this->assertEquals([
            'a' => 'a1',
            'b' => 'b1'
        ],$o->getFirstErrors());

        $o->resetErrors('a');
        $this->assertFalse($o->hasErrors('a'));
        $this->assertTrue($o->hasErrors());

        $o->resetErrors();
        $o->addError('c', 'c1');
        $this->assertTrue($o->hasErrors());

        $o->resetErrors('c');
        $this->assertFalse($o->hasErrors());
    }

}

class TraitErrorsTestC {
    use \WebComplete\core\utils\traits\TraitErrors;
}