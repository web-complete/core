<?php declare(strict_types = 1);

namespace WebComplete\core\cube;

use Psr\SimpleCache\CacheInterface;
use WebComplete\core\utils\helpers\ClassHelper;

class CubeManager
{

    const FILENAME = 'Cube.php';

    /**
     * @var ClassHelper
     */
    protected $classHelper;
    /**
     * @var CacheInterface
     */
    protected $cache;
    /**
     * @var array
     */
    protected $registered = [];

    /**
     * @param ClassHelper $classHelper
     * @param CacheInterface $cache
     */
    public function __construct(ClassHelper $classHelper, CacheInterface $cache)
    {
        $this->classHelper = $classHelper;
        $this->cache = $cache;
    }

    /**
     * @param $cubeClassName
     *
     * @return AbstractCube
     * @throws CubeException
     */
    public function getCube($cubeClassName): AbstractCube
    {
        if (!isset($this->registered[$cubeClassName])) {
            throw new CubeException($cubeClassName . ' is not registered');
        }

        return $this->registered[$cubeClassName];
    }

    /**
     * @return AbstractCube[]
     */
    public function getCubes(): array
    {
        return $this->registered;
    }

    /**
     * @param $cubeClassName
     * @param array $definitions
     *
     * @throws CubeException
     */
    public function register($cubeClassName, array &$definitions)
    {
        if (!isset($this->registered[$cubeClassName])) {
            $cube = new $cubeClassName;
            if (!$cube instanceof AbstractCube) {
                throw new CubeException($cubeClassName . ' is not an instance of ' . AbstractCube::class);
            }

            $cube->registerDependencies($definitions);
            $this->registered[$cubeClassName] = $cube;
        }
    }

    /**
     * @param string $directory
     * @param array $definitions
     *
     * @throws \Exception
     */
    public function registerAll(string $directory, array &$definitions)
    {
        $classMap = $this->findAll($directory);
        foreach ($classMap as $class) {
            $this->register($class, $definitions);
        }
    }

    /**
     * @param $directory
     *
     * @return array [file => class]
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function findAll($directory): array
    {
        $key = \str_replace(['::', '\\'], '.', __METHOD__) . '.' . \crc32($directory);
        $result = $this->cache->get($key);
        if (!$result) {
            $result = $this->classHelper->getClassMap($directory, self::FILENAME);
            $this->cache->set($key, $result);
        }
        return $result;
    }
}
