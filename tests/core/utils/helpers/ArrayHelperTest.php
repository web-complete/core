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

    public function testMerge()
    {
        $array1 = [
            'k1' => [1,2,3],
            'k2' => ['a' => 1, 'b' => 2],
            'k3' => 1,
        ];
        $array2 = [
            'k1' => [5,5],
            'k2' => ['b' => 3, 'c' => 4],
            'k4' => 2,
            5
        ];
        $arrayHelper = new ArrayHelper();
        $this->assertEquals([
            'k1' => [1,2,3,5,5],
            'k2' => ['a' => 1, 'b' => 3, 'c' => 4],
            'k3' => 1,
            'k4' => 2,
            5,
        ], $arrayHelper->merge($array1, $array2));
    }
}
