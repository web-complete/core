<?php declare(strict_types = 1);

namespace WebComplete\core\package;

use Psr\SimpleCache\CacheInterface;
use WebComplete\core\utils\helpers\ClassHelper;

class PackageManager
{

    const FILENAME = 'Package.php';

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
     * @param $packageClassName
     *
     * @return AbstractPackage
     * @throws PackageException
     */
    public function getPackage($packageClassName): AbstractPackage
    {
        if (!isset($this->registered[$packageClassName])) {
            throw new PackageException($packageClassName . ' is not registered');
        }

        return $this->registered[$packageClassName];
    }

    /**
     * @param $packageClassName
     * @param array $definitions
     *
     * @throws PackageException
     */
    public function register($packageClassName, array &$definitions)
    {
        if (!isset($this->registered[$packageClassName])) {
            $package = new $packageClassName;
            if (!$package instanceof AbstractPackage) {
                throw new PackageException($packageClassName . ' is not an instance of ' . AbstractPackage::class);
            }

            $package->registerDependencies($definitions);
            $this->registered[$packageClassName] = $package;
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
        $key = __CLASS__ . ':' . __METHOD__ . ':' . $directory;
        $result = $this->cache->get($key);
        if (!$result) {
            $result = $this->classHelper->getClassMap($directory, self::FILENAME);
            $this->cache->set($key, $result);
        }
        return $result;
    }
}
