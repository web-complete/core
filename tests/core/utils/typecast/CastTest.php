<?php

namespace tests;

use WebComplete\core\utils\typecast\Cast;
use function WebComplete\core\utils\typecast\cast;
use WebComplete\core\utils\typecast\Exception;
use WebComplete\core\utils\typecast\type\Factory;
use WebComplete\core\utils\typecast\type\TypeBool;
use WebComplete\core\utils\typecast\type\TypeFloat;

class CastTest extends \CoreTestCase
{

    public function testInt()
    {
        $this->assertNull(cast(null, Cast::INT));
        $this->assertSame(1, cast(1, Cast::INT));
        $this->assertSame(1, cast('1', Cast::INT));
    }
    
    public function testBool()
    {
        $this->assertNull(cast(null, Cast::BOOL));
        $this->assertTrue(cast(true, Cast::BOOL));
        $this->assertFalse(cast(false, Cast::BOOL));
        $this->assertTrue(cast('1', Cast::BOOL));
        $this->assertFalse(cast('0', Cast::BOOL));
    }
    
    public function testFloat()
    {
        $this->assertNull(cast(null, Cast::FLOAT));
        $this->assertSame(1.0, cast(1, Cast::FLOAT));
        $this->assertSame(1.1, cast('1.1', Cast::FLOAT));
        $this->assertSame(0.0, cast('str', Cast::FLOAT));
    }
    
    public function testString()
    {
        $this->assertNull(cast(null, Cast::STRING));
        $this->assertSame('1', cast(1, Cast::STRING));
        $this->assertSame('str', cast('str', Cast::STRING));
        $this->assertSame('', cast(false, Cast::STRING));
        $this->assertSame('1', cast(true, Cast::STRING));
    }
    
    public function testBinary()
    {
        $string = 'Строка UTF8';
        $string = cast($string, Cast::BINARY);
        $this->assertEquals(17, \mb_strlen($string, '8BIT'));
        $this->assertNull(cast(null, Cast::BINARY));
    }
    
    public function testObject()
    {
        $object = cast(['a' => 1], Cast::OBJECT);
        $this->assertInstanceOf(\stdClass::class, $object);
        $this->assertEquals(1, $object->a);
        $this->assertNull(cast(null, Cast::OBJECT));
    }
    
    public function testUnset()
    {
        $this->assertNull(cast(1, Cast::UNSET));
    }
    
    public function testArray()
    {
        $this->assertSame([], cast(null, Cast::ARRAY));
        $this->assertSame([1], cast([1], Cast::ARRAY));
        $this->assertSame([1], cast(1, Cast::ARRAY));
    }
    
    public function testArrayOfType()
    {
        $result = cast([1,2,3, '4', '5.5', 6.6, '7,7', '0', null, false, true], [Cast::FLOAT]);
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.5, 6.6, 7.0, 0.0, null, 0.0, 1.0], $result);
        $result = cast([1,2,3, '4', '5.5', 6.6, '7,7', '0', null, false, true], [new TypeFloat(0.0)]);
        $this->assertSame([1.0, 2.0, 3.0, 4.0, 5.5, 6.6, 7.0, 0.0, 0.0, 0.0, 1.0], $result);
    }

    public function testClosure()
    {
        $this->assertSame(2, cast(1, function ($value) {
            return $value + 1;
        }));
    }

    public function testArrayClosure()
    {
        $this->assertSame([2,3,4], cast([1,2,3], [function ($value) {
            return $value + 1;
        }]));
    }

    public function testScheme()
    {
        $data = [
            'field.1' => '1',
            'field.2' => '2',
            'field.3' => '3',
            'field.4' => [1,2,3],
            'field.5' => [
                'field.5.1' => '11',
                'field.5.2' => '22',
                'field.5.3' => [
                    'field.5.3.1' => '1'
                ],
                'field.5.4' => null
            ],
            'field.X' => 'x',
        ];
        $scheme = [
            'field.1' => Cast::INT,
            'field.2' => Cast::FLOAT,
            'field.3' => Cast::STRING,
            'field.4' => [Cast::BOOL],
            'field.5' => [
                'field.5.1' => Cast::FLOAT,
                'field.5.2' => function($value) { return $value+1; },
                'field.5.3' => [
                    'field.5.3.1' => Cast::BOOL
                ],
                'field.5.4' => [
                    'field.5.4.1' => Cast::BOOL
                ]
            ],
            'field.6' => Cast::INT,
        ];

        $result = cast($data, $scheme);
        $this->assertSame([
            'field.1' => 1,
            'field.2' => 2.0,
            'field.3' => '3',
            'field.4' => [true, true, true],
            'field.5' => [
                'field.5.1' => 11.0,
                'field.5.2' => 23,
                'field.5.3' => [
                    'field.5.3.1' => true
                ],
                'field.5.4' => []
            ],
            'field.X' => 'x',
        ], $result);
    }

    public function testSchemeStrict()
    {
        $data = [
            'field.1' => '1',
            'field.2' => '2',
            'field.3' => '3',
            'field.4' => [1,2,3],
            'field.5' => [
                'field.5.1' => '11',
                'field.5.2' => '22',
                'field.5.3' => [
                    'field.5.3.1' => '1'
                ],
                'field.5.4' => null
            ],
            'field.X' => 'x',
        ];
        $scheme = [
            'field.1' => Cast::INT,
            'field.2' => Cast::FLOAT,
            'field.3' => Cast::STRING,
            'field.4' => [Cast::BOOL],
            'field.5' => [
                'field.5.1' => Cast::FLOAT,
                'field.5.2' => function($value) { return $value+1; },
                'field.5.3' => [
                    'field.5.3.1' => Cast::BOOL
                ],
                'field.5.4' => [
                    'field.5.4.1' => new TypeBool(false)
                ]
            ],
            'field.6' => Cast::INT,
        ];

        $result = cast($data, $scheme, true);
        $this->assertSame([
            'field.1' => 1,
            'field.2' => 2.0,
            'field.3' => '3',
            'field.4' => [true, true, true],
            'field.5' => [
                'field.5.1' => 11.0,
                'field.5.2' => 23,
                'field.5.3' => [
                    'field.5.3.1' => true
                ],
                'field.5.4' => [
                    'field.5.4.1' => false
                ]
            ],
            'field.6' => null,
        ], $result);
    }

    public function testFactoryException()
    {
        $this->expectException(Exception::class);
        $factory = new Factory();
        $factory->createType('unknown');
    }
}
