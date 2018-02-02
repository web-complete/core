<?php declare(strict_types = 1);

namespace WebComplete\core\cube;

use WebComplete\core\utils\cache\CacheService;
use WebComplete\core\utils\container\ContainerInterface;
use WebComplete\core\utils\helpers\ClassHelper;

class CubeManager
{

    const FILENAME = 'Cube.php';

    /**
     * @var ClassHelper
     */
    protected $classHelper;
    /**
     * @var CacheService
     */
    protected $cacheService;
    /**
     * @var array
     */
    protected $registered = [];

    /**
     * @param ClassHelper $classHelper
     * @param CacheService $cacheService
     */
    public function __construct(ClassHelper $classHelper, CacheService $cacheService)
    {
        $this->classHelper = $classHelper;
        $this->cacheService = $cacheService;
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

            if ($cube->enabled) {
                $cube->registerDependencies($definitions);
                $this->registered[$cubeClassName] = $cube;
            }
        }
    }

    /**
     * @param string $directory
     * @param array $definitions
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \WebComplete\core\cube\CubeException
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
        $cache = $this->cacheService->systemSimple();
        $key = \str_replace(['::', '\\'], '_', __METHOD__) . '_' . \crc32($directory);
        $result = $cache->get($key);
        if (!$result) {
            $result = $this->classHelper->getClassMap($directory, self::FILENAME);
            $cache->set($key, $result);
        }
        return $result;
    }

    /**
     * @param ContainerInterface $container
     */
    public function bootstrap(ContainerInterface $container)
    {
        foreach ($this->getCubes() as $cube) {
            $cube->bootstrap($container);
        }
    }
}
