<?php

namespace WebComplete\core\cube;

use Mvkasatkin\mocker\Mocker;

class CubeTest extends \CoreTestCase
{

    public function testGetMigrations()
    {
        /** @var AbstractCube $cube */
        $cube = Mocker::create(AbstractCube::class);
        $this->assertEquals([], $cube->getMigrations());
    }
}