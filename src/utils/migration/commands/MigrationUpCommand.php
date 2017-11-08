<?php

namespace WebComplete\core\utils\migration\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WebComplete\core\cube\CubeManager;
use WebComplete\core\utils\migration\MigrationService;

class MigrationUpCommand extends Command
{

    /**
     * @var CubeManager
     */
    protected $cubeManager;
    /**
     * @var MigrationService
     */
    protected $migrationService;

    /**
     * @param CubeManager $cubeManager
     * @param MigrationService $migrationService
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(CubeManager $cubeManager, MigrationService $migrationService)
    {
        parent::__construct();
        $this->cubeManager = $cubeManager;
        $this->migrationService = $migrationService;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('migrate:up')
            ->setDescription('Execute migration[s]')
            ->addArgument('class', InputArgument::OPTIONAL, 'Migration class');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($class = $input->getArgument('class')) {
            $newMigrations = [$class];
        } else {
            $newMigrations = $this->getNewMigrations();
        }

        if (!$newMigrations) {
            $output->writeln('New migrations not found');
            return null;
        }

        $message = "New migrations: \n";
        foreach ($newMigrations as $migrationClass) {
            $message .= $migrationClass . "\n";
        }
        $output->writeln($message . "\n");

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue? (Y/N): ', false);
        if ($class || $helper->ask($input, $output, $question)) {
            foreach ($newMigrations as $migrationClass) {
                $output->writeln('Run: ' . $migrationClass);
                $this->migrationService->up($migrationClass);
            }
            $output->writeln('Done!');
            return null;
        }

        $output->writeln('Cancel');
        return null;
    }

    /**
     * @return array
     */
    protected function getNewMigrations(): array
    {
        $cubes = $this->cubeManager->getCubes();
        $migrations = [[]];
        foreach ($cubes as $cube) {
            $migrations[] = $cube->getMigrations();
        }
        $allMigrations = \array_merge(...$migrations);
        \ksort($allMigrations, \SORT_STRING & \SORT_ASC);

        $newMigrations = [];
        foreach ($allMigrations as $k => $migrationClass) {
            if (!$this->migrationService->isRegistered($migrationClass)) {
                $newMigrations[$k] = $migrationClass;
            }
        }

        return $newMigrations;
    }
}
