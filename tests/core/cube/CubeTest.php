<?php

namespace WebComplete\core\cube;

use Mvkasatkin\mocker\Mocker;
use WebComplete\core\utils\container\ContainerAdapter;

class CubeTest extends \CoreTestCase
{

    public function testGetMigrations()
    {
        /** @var AbstractCube $cube */
        $cube = Mocker::create(AbstractCube::class);
        $cube->bootstrap(new ContainerAdapter());
        $this->assertEquals([], $cube->getMigrations());
    }
}