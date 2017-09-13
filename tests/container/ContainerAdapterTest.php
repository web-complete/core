<?php

class ContainerAdapterTest extends \PHPUnit\Framework\TestCase
{

    public function testHas()
    {
        $container = new \WebComplete\core\container\ContainerAdapter();
        $e = null;
        try { $container->has('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->has('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $dic = $this->getMockForAbstractClass(\Psr\Container\ContainerInterface::class);
        $dic->expects($this->once())->method('has')->with('a')->willReturn(true);
        $container->setContainer($dic);
        $this->assertEquals(true, $container->has('a'));
    }

    public function testGet()
    {
        $container = new \WebComplete\core\container\ContainerAdapter();
        $e = null;
        try { $container->get('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->get('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $dic = $this->getMockForAbstractClass(\Psr\Container\ContainerInterface::class);
        $dic->expects($this->once())->method('get')->with('a')->willReturn('b');
        $container->setContainer($dic);
        $this->assertEquals('b', $container->get('a'));
    }

    public function testMake()
    {
        $container = new \WebComplete\core\container\ContainerAdapter();
        $e = null;
        try { $container->make('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $container->setContainer(new stdClass());
        $e = null;
        try { $container->make('a'); }
        catch (\Exception $e) { }
        $this->assertInstanceOf(\WebComplete\core\container\ContainerException::class, $e);

        $dic = $this->createMock(ContainerAdapterTestC::class);
        $dic->expects($this->once())->method('make')->with('a')->willReturn('b');
        $container->setContainer($dic);
        $this->assertEquals('b', $container->make('a'));
    }

}

class ContainerAdapterTestC {
    public function make() {}
}