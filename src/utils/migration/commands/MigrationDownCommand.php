<?php

namespace WebComplete\core\utils\migration\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WebComplete\core\cube\CubeManager;
use WebComplete\core\utils\migration\MigrationService;

class MigrationDownCommand extends Command
{

    /**
     * @var MigrationService
     */
    protected $migrationService;

    /**
     * @param MigrationService $migrationService
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(MigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('migrate:down')
            ->setDescription('Rollback migration[s]')
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
            $rollbackMigrations = [$class];
        } else {
            $rollbackMigrations = $this->migrationService->getRegistered();
            $rollbackMigrations = \array_reverse($rollbackMigrations, true);
        }

        if (!$rollbackMigrations) {
            $output->writeln('Migrations not found');
            return null;
        }

        $message = "Rollback migrations: \n";
        foreach ($rollbackMigrations as $migrationClass) {
            $message .= $migrationClass . "\n";
        }
        $output->writeln($message . "\n");

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue? (Y/N): ', false);
        if ($class || $helper->ask($input, $output, $question)) {
            foreach ($rollbackMigrations as $migrationClass) {
                $output->writeln('Rollback: ' . $migrationClass);
                $this->migrationService->down($migrationClass);
            }
            $output->writeln('Done!');
            return null;
        }

        $output->writeln('Cancel');
        return null;
    }
}
