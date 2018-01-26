<?php

namespace WebComplete\core\utils\cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheCommand extends Command
{
    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @param CacheService $cacheService
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct(null);
        $this->cacheService = $cacheService;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('cache:clear')->setDescription('Clear all cache');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cacheService->system()->clear();
        $this->cacheService->user()->clear();
        $output->writeln('Done!');
        return null;
    }
}
