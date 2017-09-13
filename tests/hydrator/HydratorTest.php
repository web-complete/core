<?php

class HydratorTest extends \PHPUnit\Framework\TestCase
{

    protected $map = ['v_1' => 'a1', 'v_2' => 'b2', 'v_3' => 'b3'];

    public function testExtract()
    {
        $o = new HydratorTestClass();
        $o->a1 = 'value1';
        $o->setB2('value2');
        $o->setB3('value3');
        $this->assertEquals(['v_1' => 'value1', 'v_2' => 'value2', 'v_3' => 'value3'],
            (new \WebComplete\core\hydrator\Hydrator())->extract($o, $this->map));
    }

    public function testHydrateClass()
    {
        $data = ['v_1' => 'value1', 'v_2' => 'value2', 'v_3' => 'value3'];
        /** @var HydratorTestClass $o */
        $o = (new \WebComplete\core\hydrator\Hydrator())->hydrate($data, HydratorTestClass::class, $this->map);
        $this->assertEquals('value1', $o->a1);
        $this->assertEquals('value2', $o->getB2());
        $this->assertEquals('value3', $o->getB3());
    }

    public function testHydrateObject()
    {
        $data = ['v_1' => 'value1', 'v_2' => 'value2', 'v_3' => 'value3', 'v_4' => 'value4'];
        $o = new HydratorTestClass();
        $o->a1 = 'some';
        $o = (new \WebComplete\core\hydrator\Hydrator())->hydrate($data, $o, $this->map);
        $this->assertEquals('value1', $o->a1);
        $this->assertEquals('value2', $o->getB2());
        $this->assertEquals('value3', $o->getB3());
    }

    public function testExtractWithoutMap()
    {
        $o = new HydratorTestClass();
        $o->a1 = 'value1';
        $o->setB2('value2');
        $o->setB3('value3');
        $this->assertEquals(['a1' => 'value1', 'b2' => 'value2', 'b3' => 'value3'],
            (new \WebComplete\core\hydrator\Hydrator())->extract($o));
    }

    public function testHydrateWithoutMap()
    {
        $data = ['b2' => 'value2', 'b3' => 'value3', 'b4' => 'value4'];
        /** @var HydratorTestClass $o */
        $o = (new \WebComplete\core\hydrator\Hydrator())->hydrate($data, HydratorTestClass::class);
        $this->assertEquals(null, $o->a1);
        $this->assertEquals('value2', $o->getB2());
        $this->assertEquals('value3', $o->getB3());
    }

}

class HydratorTestClass {
    public    $a1;
    protected $b2;
    private   $b3;

    public function getB2() { return $this->b2; }
    public function setB2($b2) { $this->b2 = $b2; }
    public function getB3() { return $this->b3; }
    public function setB3($b3) { $this->b3 = $b3; }
}