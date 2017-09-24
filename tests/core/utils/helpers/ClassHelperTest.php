<?php

namespace WebComplete\core\utils\helpers;

use ClassHelperFixNS1\ClassHelperFix1;
use ClassHelperFixNS2\ClassHelperFix2;
use PHPUnit\Framework\TestCase;

class ClassHelperTest extends TestCase
{

    public function testGetClassMap()
    {
        $classHelper = new ClassHelper();
        $expected = [
            'class_helper/ClassHelperFix1.php' => ClassHelperFix1::class,
            'class_helper/sub/ClassHelperFix2.php' => ClassHelperFix2::class,
        ];
        $map = $classHelper->getClassMap(__DIR__ . '/../../../fixtures/class_helper');
        $this->assertCount(2, $map);
        $this->assertEquals(array_values($expected), array_values($map));
        $expected = array_keys($expected);
        $map = array_keys($map);
        $this->assertContains($expected[0], $map[0]);
        $this->assertContains($expected[1], $map[1]);
    }
}
