<?php

namespace WebComplete\core\utils\migration;

use Doctrine\DBAL\Connection;
use WebComplete\core\utils\container\ContainerInterface;

class MigrationRegistryMysql extends AbstractMigrationRegistry
{
    /**
     * @var Connection
     */
    protected $db;
    protected $migrationTable = '_migrations';

    /**
     * @param ContainerInterface $container
     * @param Connection $db
     */
    public function __construct(ContainerInterface $container, Connection $db)
    {
        parent::__construct($container);
        $this->db = $db;
    }

    /**
     * check or create initial registry
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function initRegistry()
    {
        $this->db->executeQuery($this->getInitialSql());
    }

    /**
     * @return string[] migration classes
     * @throws \Exception
     */
    public function getRegistered(): array
    {
        $this->initRegistry();
        $result = [];
        $stmt = $this->db->createQueryBuilder()
            ->select('*')->from($this->migrationTable)->orderBy('id', 'asc')->execute();
        if ($rows = $stmt->fetchAll(\PDO::FETCH_ASSOC)) {
            foreach ($rows as $row) {
                if (isset($row['class'])) {
                    $result[] = $row['class'];
                }
            }
        }
        return $result;
    }

    /**
     * @param string $class migration class
     *
     * @return bool
     */
    public function isRegistered(string $class): bool
    {
        return \in_array($class, $this->getRegistered(), true);
    }

    /**
     * @param string $class migration class
     * @throws \Exception
     */
    public function register(string $class)
    {
        $this->initRegistry();
        if (!$this->isRegistered($class)) {
            $migration = $this->getMigration($class);
            $migration->up();
            $this->db->insert($this->migrationTable, ['class' => $class]);
        }
    }

    /**
     * @param string $class migration class
     * @throws \Exception
     */
    public function unregister(string $class)
    {
        $this->initRegistry();
        if ($this->isRegistered($class)) {
            $migration = $this->getMigration($class);
            $migration->down();
            $this->db->delete($this->migrationTable, ['class' => $class]);
        }
    }

    /**
     * @return string
     */
    protected function getInitialSql(): string
    {
        return "CREATE TABLE IF NOT EXISTS `{$this->migrationTable}` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `class` VARCHAR(300) NOT NULL,
          `created_on` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY(`id`),
          UNIQUE(`class`)
        )";
    }
}
