<?php

require_once 'include/UserRepositoryMicro.php';
require_once 'include/UserService.php';
require_once 'include/UserFactory.php';
require_once 'include/User.php';

use tests\entity\User;
use tests\entity\UserFactory;
use tests\entity\UserRepositoryMicro;
use tests\entity\UserService;
use WebComplete\core\condition\Condition;
use WebComplete\core\condition\ConditionMicroDbParser;
use WebComplete\core\utils\container\ContainerAdapter;
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

    public function testCreate()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $this->assertEquals(3, $service->count());
    }

    public function testUpdate()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $user3->setActive(false);
        $service->save($user3);
        $user1 = $service->findById(1);
        $user2 = $service->findById(2);
        $user3 = $service->findById(3);
        $this->assertTrue($user1->getActive());
        $this->assertFalse($user2->getActive());
        $this->assertFalse($user3->getActive());
    }

    public function testDelete()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $this->assertEquals(3, $service->count());
        $service->delete(2);
        $this->assertEquals(2, $service->count());
    }

    public function testDeleteAll()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $this->assertEquals(3, $service->count());
        $service->deleteAll();
        $this->assertEquals(0, $service->count());
    }

    public function testGetMap()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $this->assertEquals([
            1 => 'User 1',
            2 => 'User 2',
            3 => 'User 3',
        ], $service->getMap('name'));
    }

    public function testFindAll()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $condition = new Condition(['active' => true]);
        $condition->addSort('name', SORT_DESC);
        $users = $service->findAll($condition);
        $this->assertCount(2, $users);
        /** @var User $expected1 */
        /** @var User $expected2 */
        $expected1 = \array_shift($users);
        $expected2 = \array_shift($users);
        $this->assertEquals(3, $expected1->getId());
        $this->assertEquals(1, $expected2->getId());
    }

    public function testFindOne()
    {
        $service = $this->getService();
        /** @var User $user1 */
        /** @var User $user2 */
        /** @var User $user3 */
        $user1 = $service->createFromData(['name' => 'User 1', 'type' => 'admin', 'active' => true]);
        $user2 = $service->createFromData(['name' => 'User 2', 'type' => 'admin', 'active' => false]);
        $user3 = $service->createFromData(['name' => 'User 3', 'type' => 'moderator', 'active' => true]);
        $service->save($user1);
        $service->save($user2);
        $service->save($user3);
        $condition = new Condition(['active' => true]);
        $condition->addSort('name', SORT_DESC);
        /** @var User $user */
        $user = $service->findOne($condition);
        $this->assertEquals(3, $user->getId());
    }

    public function testTransaction()
    {
        $result = false;
        $service = $this->getService();
        $service->transaction(function () use (&$result) {
            $result = true;
        });
        $this->assertTrue($result);
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
