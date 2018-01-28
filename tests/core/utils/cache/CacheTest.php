<?php

namespace WebComplete\core\utils\cache;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CacheTest extends \CoreTestCase
{

    public function testSetGet()
    {
        $cacheService = new CacheService(new ArrayAdapter(), new ArrayAdapter());
        Cache::setCacheService($cacheService);
        $this->assertNull(Cache::get('test1'));
        Cache::set('test1', 'value1', null, ['tag1']);
        $this->assertEquals('value1', Cache::get('test1'));
        Cache::invalidateTags(['tag2']);
        $this->assertEquals('value1', Cache::get('test1'));
        Cache::invalidateTags(['tag1']);
        $this->assertNull(Cache::get('test1'));

        $this->assertNull(Cache::get('test2'));
        Cache::set('test2', 'value2');
        $this->assertEquals('value2', Cache::get('test2'));
        Cache::invalidate('test2');
        $this->assertNull(Cache::get('test2'));

        Cache::set('test21', 'value21');
        Cache::clear();
        $this->assertNull(Cache::get('test21'));
    }

    public function testSetGetClosure()
    {
        $cacheService = new CacheService(new ArrayAdapter(), new ArrayAdapter());
        Cache::setCacheService($cacheService);
        $this->assertEquals('value3', Cache::getOrSet('test3', function () {
            return 'value3';
        }));
        $this->assertEquals('value3', Cache::getOrSet('test3', function () {
            return 'value4';
        }));
    }

    public function testHtml()
    {
        $cacheService = new CacheService(new ArrayAdapter(), new ArrayAdapter());
        Cache::setCacheService($cacheService);
        $html = Cache::html('html1');
        ob_start();
        $this->assertNotNull($html);
        echo 'html_test1';
        $html->end();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('html_test1', $content);

        ob_start();
        $html = Cache::html('html1');
        $this->assertNull($html);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('html_test1', $content);
    }

    public function testSimple()
    {
        $cacheService = new CacheService(new ArrayAdapter(), new ArrayAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $cacheService->system());
        $this->assertInstanceOf(AdapterInterface::class, $cacheService->user());
        $this->assertInstanceOf(CacheInterface::class, $cacheService->systemSimple());
        $this->assertInstanceOf(CacheInterface::class, $cacheService->userSimple());
    }

    public function testKey()
    {
        $this->assertEquals('__var1___val1___var2___val2__', Cache::key(['var1' => 'val1', 'var2' => 'val2']));
    }
}
