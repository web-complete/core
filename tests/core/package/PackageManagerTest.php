<?php

namespace WebComplete\core\package;

use Mvkasatkin\mocker\Mocker;
use WebComplete\core\utils\helpers\ClassHelper;

class PackageManagerTest extends \CoreTestCase
{

    public function testInstance()
    {
        $classHelper = new ClassHelper();
        $pm = new PackageManager($classHelper);
        $this->assertInstanceOf(PackageManager::class, $pm);
    }

    public function testGetPackage()
    {
        $p = Mocker::create(AbstractPackage::class);
        $classHelper = new ClassHelper();
        $pm = new PackageManager($classHelper);
        $def = [];
        $pm->register(get_class($p), $def);
        $this->assertInstanceOf(get_class($p), $pm->getPackage(get_class($p)));
    }

    public function testGetPackageException()
    {
        $this->expectException(PackageException::class);
        $classHelper = new ClassHelper();
        $pm = new PackageManager($classHelper);
        $pm->getPackage('asd');
    }

    public function testRegisterException()
    {
        $this->expectException(PackageException::class);
        $p = Mocker::create(\stdClass::class);
        $classHelper = new ClassHelper();
        $pm = new PackageManager($classHelper);
        $def = [];
        $pm->register(get_class($p), $def);
    }

    public function testRegisterAll()
    {
        $dir = 'SomeDir';
        $def = [];
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class, [
            Mocker::method('getClassMap', 1, [$dir, PackageManager::FILENAME])->returns([
                'SomeDir/SomeFile' => 'SomeClass'
            ])
        ]);
        /** @var PackageManager $pm */
        $pm = Mocker::create(PackageManager::class, [
            Mocker::method('register', 1, ['SomeClass', $def])
        ], [$classHelper]);
        $pm->registerAll($dir, $def);
    }

    public function testFindAll()
    {
        $dir = 'SomeDir';
        /** @var ClassHelper $classHelper */
        $classHelper = Mocker::create(ClassHelper::class, [
            Mocker::method('getClassMap', 1, [$dir, PackageManager::FILENAME])->returns([])
        ]);
        $pm = new PackageManager($classHelper);
        $pm->findAll($dir);
    }
}
