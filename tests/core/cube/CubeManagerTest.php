<?php

namespace WebComplete\core\cube;

use Mvkasatkin\mocker\Mocker;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Simple\NullCache;
use WebComplete\core\utils\cache\CacheService;
use WebComplete\core\utils\container\ContainerAdapter;
use WebComplete\core\utils\helpers\ClassHelper;

class CubeManagerTest extends \CoreTestCase
{

    public function testInstance()
    {
        $classHelper = new ClassHelper();
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
        $this->assertInstanceOf(CubeManager::class, $pm);
    }

    public function testGetCube()
    {
        $p = Mocker::create(AbstractCube::class);
        $classHelper = new ClassHelper();
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
        $def = [];
        $pm->register(get_class($p), $def);
        $this->assertInstanceOf(get_class($p), $pm->getCube(get_class($p)));
    }

    public function testGetCubes()
    {
        $p = Mocker::create(AbstractCube::class);
        $classHelper = new ClassHelper();
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
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
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
        $pm->getCube('asd');
    }

    public function testRegisterException()
    {
        $this->expectException(CubeException::class);
        $p = Mocker::create(\stdClass::class);
        $classHelper = new ClassHelper();
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
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
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        /** @var CubeManager $pm */
        $pm = Mocker::create(CubeManager::class, [
            Mocker::method('register', 1, ['SomeClass', $def])
        ], [$classHelper, $cacheService]);
        $pm->registerAll($dir, $def);
    }

    public function testFindAll()
    {
        $dir = 'SomeDir';
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class, [
            Mocker::method('getClassMap', 1, [$dir, CubeManager::FILENAME])->returns([])
        ]);
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
        $pm->findAll($dir);
    }

    public function testBootstrap()
    {
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class);
        $cacheService = new CacheService(new NullAdapter(), new NullAdapter());
        $pm = new CubeManager($classHelper, $cacheService);
        $registered = [
            Mocker::create(AbstractCube::class, [
                Mocker::method('bootstrap', 1)
            ]),
            Mocker::create(AbstractCube::class, [
                Mocker::method('bootstrap', 1)
            ]),
        ];
        Mocker::setProperty($pm, 'registered', $registered);
        $pm->bootstrap(new ContainerAdapter());
    }
}
