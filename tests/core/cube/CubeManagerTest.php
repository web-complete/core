<?php

namespace WebComplete\core\cube;

use Mvkasatkin\mocker\Mocker;
use Symfony\Component\Cache\Simple\NullCache;
use WebComplete\core\utils\helpers\ClassHelper;

class CubeManagerTest extends \CoreTestCase
{

    public function testInstance()
    {
        $classHelper = new ClassHelper();
        $pm = new CubeManager($classHelper, new NullCache());
        $this->assertInstanceOf(CubeManager::class, $pm);
    }

    public function testGetCube()
    {
        $p = Mocker::create(AbstractCube::class);
        $classHelper = new ClassHelper();
        $pm = new CubeManager($classHelper, new NullCache());
        $def = [];
        $pm->register(get_class($p), $def);
        $this->assertInstanceOf(get_class($p), $pm->getCube(get_class($p)));
    }

    public function testGetCubes()
    {
        $p = Mocker::create(AbstractCube::class);
        $classHelper = new ClassHelper();
        $pm = new CubeManager($classHelper, new NullCache());
        $def = [];
        $pm->register(get_class($p), $def);
        $this->assertEquals([
            get_class($p) => $p
        ], $pm->getCubes());
    }

    public function testGetCubeException()
    {
        $this->expectException(CubeException::class);
        $classHelper = new ClassHelper();
        $pm = new CubeManager($classHelper, new NullCache());
        $pm->getCube('asd');
    }

    public function testRegisterException()
    {
        $this->expectException(CubeException::class);
        $p = Mocker::create(\stdClass::class);
        $classHelper = new ClassHelper();
        $pm = new CubeManager($classHelper, new NullCache());
        $def = [];
        $pm->register(get_class($p), $def);
    }

    public function testRegisterAll()
    {
        $dir = 'SomeDir';
        $def = [];
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class, [
            Mocker::method('getClassMap', 1, [$dir, CubeManager::FILENAME])->returns([
                'SomeDir/SomeFile' => 'SomeClass'
            ])
        ]);
        /** @var CubeManager $pm */
        $pm = Mocker::create(CubeManager::class, [
            Mocker::method('register', 1, ['SomeClass', $def])
        ], [$classHelper, new NullCache()]);
        $pm->registerAll($dir, $def);
    }

    public function testFindAll()
    {
        $dir = 'SomeDir';
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class, [
            Mocker::method('getClassMap', 1, [$dir, CubeManager::FILENAME])->returns([])
        ]);
        $pm = new CubeManager($classHelper, new NullCache());
        $pm->findAll($dir);
    }
}
