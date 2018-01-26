<?php

namespace WebComplete\core\utils\helpers;

use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{

    public function testSetGetValue()
    {
        $data = [];
        $arrayHelper = new ArrayHelper();
        $arrayHelper->setValue($data, 'some.path1', 'value1');
        $this->assertEquals(['some' => ['path1' => 'value1']], $data);
        $arrayHelper->setValue($data, 'some.path1', 'value2');
        $this->assertEquals(['some' => ['path1' => 'value2']], $data);
        $this->assertEquals('value2', $arrayHelper->getValue($data, 'some.path1'));
    }
}
