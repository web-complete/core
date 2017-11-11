<?php

require_once 'include/UserRepositoryMicro.php';
require_once 'include/UserService.php';
require_once 'include/UserFactory.php';
require_once 'include/User.php';

use tests\entity\UserFactory;
use tests\entity\UserRepositoryMicro;
use tests\entity\UserService;
use WebComplete\core\condition\ConditionMicroDbParser;
use WebComplete\core\utils\container\ContainerAdapter;
use WebComplete\core\utils\hydrator\Hydrator;
use WebComplete\microDb\MicroDb;

/**
 * integration test
 */
class AbstractEntityRepositoryMicroTest extends CoreTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->clearDir();
    }

    public function tearDown()
    {
        $this->clearDir();
        parent::tearDown();
    }

    public function test()
    {
    }

    protected function getService(): UserService
    {
        $microDb = new MicroDb(__DIR__ . '/storage', 'test-db');
        $container = new ContainerAdapter(\DI\ContainerBuilder::buildDevContainer());
        $factory = new UserFactory($container);
        $repository = new UserRepositoryMicro($factory, $microDb, new ConditionMicroDbParser());
        return new UserService($repository);
    }

    protected function clearDir()
    {
        @\unlink(__DIR__ . '/storage/test-db_users.fdb');
        @\rmdir(__DIR__ . '/storage');
    }
}
