<?php

namespace tests\core\utils\migration;

use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Mvkasatkin\mocker\Mocker;
use WebComplete\core\utils\container\ContainerAdapter;
use WebComplete\core\utils\container\ContainerInterface;
use WebComplete\core\utils\migration\MigrationInterface;
use WebComplete\core\utils\migration\MigrationRegistryInterface;
use WebComplete\core\utils\migration\MigrationRegistryMysql;
use WebComplete\core\utils\migration\MigrationService;
use PHPUnit\Framework\TestCase;

class MigrationServiceTest extends \CoreTestCase
{

    /** @var ContainerInterface */
    protected $container;
    /** @var  Connection */
    protected $db;

    public function setUp()
    {
        parent::setUp();
        $di = ContainerBuilder::buildDevContainer();
        $this->container = new ContainerAdapter($di);
        $this->db = DriverManager::getConnection(['url' => 'sqlite:///:memory:']);
    }

    public function testInstance()
    {
        $migrationRegistry = new MigrationRegistryMysql($this->container, $this->db);
        $migrationService = new MigrationService($migrationRegistry);
        $this->assertInstanceOf(MigrationService::class, $migrationService);
    }

    public function testUpAll()
    {
        /** @var MigrationRegistryInterface $registry */
        $registry = Mocker::create(MigrationRegistryInterface::class, [
            Mocker::method('register', 1, 'testClass'),
        ]);
        $service = new MigrationService($registry);
        $service->upAll(['testClass']);
    }

    public function testDownAll()
    {
        /** @var MigrationRegistryInterface $registry */
        $registry = Mocker::create(MigrationRegistryInterface::class, [
            Mocker::method('getRegistered', 1)->returns(['testClass']),
            Mocker::method('unregister', 1, 'testClass'),
        ]);
        $service = new MigrationService($registry);
        $service->downAll();
    }

    public function testInit()
    {
        $res = $this->db
            ->executeQuery("SELECT name FROM sqlite_master WHERE type='table' AND name='_migrations'")
            ->fetchAll();
        $this->assertCount(0, $res);
        $migrationRegistry = $this->createSqliteRegistry();
        $migrationRegistry->getRegistered();
        $res = $this->db
            ->executeQuery("SELECT name FROM sqlite_master WHERE type='table' AND name='_migrations'")
            ->fetchAll();
        $this->assertCount(1, $res);
    }

    public function testRegister()
    {
        $migration = Mocker::create(MigrationInterface::class, [
            Mocker::method('up', 1),
            Mocker::method('down', 0)
        ]);
        $this->container->set('testClass', $migration);
        $migrationRegistry = $this->createSqliteRegistry();
        $migrationRegistry->register('testClass');
        $migrationRegistry->register('testClass');
    }

    public function testUnregister()
    {
        $migration = Mocker::create(MigrationInterface::class, [
            Mocker::method('up', 1),
            Mocker::method('down', 1)
        ]);
        $this->container->set('testClass', $migration);
        $migrationRegistry = $this->createSqliteRegistry();
        $migrationRegistry->register('testClass');
        $migrationRegistry->unregister('testClass');
        $migrationRegistry->unregister('testClass');
    }

    public function testNotAMigration()
    {
        $this->expectException(\RuntimeException::class);
        $this->container->set('testClass', new \stdClass());
        $migrationRegistry = $this->createSqliteRegistry();
        $migrationRegistry->register('testClass');
    }

    /**
     * @return MigrationRegistryMysql
     */
    protected function createSqliteRegistry(): MigrationRegistryMysql
    {
        /** @var MigrationRegistryMysql $migrationRegistry */
        $migrationRegistry = Mocker::create(MigrationRegistryMysql::class, [
            Mocker::method('getInitialSql')->returns("CREATE TABLE IF NOT EXISTS `_migrations` (
              `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL ,
              `class` varchar(300) NOT NULL)"),
        ],[$this->container, $this->db]);
        return $migrationRegistry;
    }

}
