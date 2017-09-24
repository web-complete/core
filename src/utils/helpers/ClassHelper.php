<?php

namespace WebComplete\core\utils\helpers;

use hanneskod\classtools\Iterator\ClassIterator;
use Symfony\Component\Finder\Finder;

class ClassHelper
{

    /**
     * @param string $directory
     * @param string $namePattern
     *
     * @return array
     * @throws \Exception
     */
    public function getClassMap(string $directory, string $namePattern = '*.php'): array
    {
        $result = [];
        $finder = new Finder();
        $finder->in($directory)->files()->name($namePattern);
        $iterator = new ClassIterator($finder);
        foreach ($iterator->getClassMap() as $class => $splFileInfo) {
            $result[$splFileInfo->getRealPath()] = $class;
        }
        return $result;
    }
}
