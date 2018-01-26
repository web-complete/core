<?php

namespace WebComplete\core\utils\cache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Simple\Psr6Cache;

class CacheService
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $systemCache;
    /**
     * @var CacheItemPoolInterface
     */
    protected $userCache;
    /**
     * @var CacheInterface
     */
    protected $systemCacheSimple;
    /**
     * @var CacheInterface
     */
    protected $userCacheSimple;

    /**
     * @param CacheItemPoolInterface $systemCache
     * @param CacheItemPoolInterface $userCache
     */
    public function __construct(CacheItemPoolInterface $systemCache, CacheItemPoolInterface $userCache)
    {
        $this->systemCache = $systemCache;
        $this->userCache = $userCache instanceof AdapterInterface
            ? new TagAwareAdapter($userCache, $userCache)
            : $userCache;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function system(): CacheItemPoolInterface
    {
        return $this->systemCache;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function user(): CacheItemPoolInterface
    {
        return $this->userCache;
    }

    /**
     * @return CacheInterface
     */
    public function systemSimple(): CacheInterface
    {
        if (!$this->systemCacheSimple) {
            $this->systemCacheSimple = new Psr6Cache($this->systemCache);
        }
        return $this->systemCacheSimple;
    }

    /**
     * @return CacheInterface
     */
    public function userSimple(): CacheInterface
    {
        if (!$this->userCacheSimple) {
            $this->userCacheSimple = new Psr6Cache($this->userCache);
        }
        return $this->userCacheSimple;
    }
}
