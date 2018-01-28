<?php

namespace WebComplete\core\utils\cache;

class CacheRuntimeTest extends \CoreTestCase
{

    public function testSetGet()
    {
        $this->assertNull(CacheRuntime::get('test1'));
        CacheRuntime::set('test1', 'value1');
        $this->assertEquals('value1', CacheRuntime::get('test1'));
        CacheRuntime::invalidate('test1');
        $this->assertNull(CacheRuntime::get('test1'));

        CacheRuntime::set('test2', 'value2');
        CacheRuntime::clear();
        $this->assertNull(CacheRuntime::get('test2'));
    }

    public function testClosure()
    {
        $this->assertEquals('value3', CacheRuntime::getOrSet('test3', function () {
            return 'value3';
        }));
        $this->assertEquals('value3', CacheRuntime::getOrSet('test3', function () {
            return 'value4';
        }));
        CacheRuntime::clear();
    }
}