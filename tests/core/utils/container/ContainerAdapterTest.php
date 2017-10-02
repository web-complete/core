<?php

use Mvkasatkin\mocker\Mocker;
use WebComplete\core\utils\container\ContainerInterface;

class ContainerAdapterTest extends CoreTestCase
{

    public function testHas()
    {
        $container = new \WebComplete\core\utils\container\ContainerAdapter();
        $e = null;
        try { $container->has('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->has('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $dic = $this->getMockForAbstractClass(\Psr\Container\ContainerInterface::class);
        $dic->expects($this->once())->method('has')->with('a')->willReturn(true);
        $container->setContainer($dic);
        $this->assertEquals(true, $container->has('a'));
    }

    public function testGet()
    {
        $container = new \WebComplete\core\utils\container\ContainerAdapter();
        $e = null;
        try { $container->get('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->get('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $dic = $this->getMockForAbstractClass(\Psr\Container\ContainerInterface::class);
        $dic->expects($this->once())->method('get')->with('a')->willReturn('b');
        $container->setContainer($dic);
        $this->assertEquals('b', $container->get('a'));
    }

    public function testSet()
    {
        $container = Mocker::create(ContainerInterface::class, [
            Mocker::method('set', 1, ['aaa', 'bbb'])
        ]);
        $containerAdapter = new \WebComplete\core\utils\container\ContainerAdapter();
        $containerAdapter->setContainer($container);
        $containerAdapter->set('aaa', 'bbb');
    }

    public function testMake()
    {
        $container = new \WebComplete\core\utils\container\ContainerAdapter();
        $e = null;
        try { $container->make('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->make('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\utils\container\ContainerException::class, $e);

        $dic = $this->createMock(ContainerAdapterTestC::class);
        $dic->expects($this->once())->method('make')->with('a')->willReturn('b');
        $container->setContainer($dic);
        $this->assertEquals('b', $container->make('a'));
    }

}

class ContainerAdapterTestC {
    public function make() {}
}