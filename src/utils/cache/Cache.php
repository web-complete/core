<?php

namespace WebComplete\core\utils\cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;

class Cache
{
    const TTL_1MIN  = 60;
    const TTL_5MIN  = 300;
    const TTL_10MIN = 600;
    const TTL_HOUR  = 3600;
    const TTL_DAY   = 86400;
    const TTL_WEEK  = 604800;
    const TTL_MONTH = 2592000;

    /**
     * @var CacheService
     */
    protected static $cacheService;

    /**
     * @param string|array $key
     *
     * @return mixed|null
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function get($key)
    {
        $item = self::getCacheService()->user()->getItem(self::key($key));
        if ($item->isHit()) {
            return $item->get();
        }
        return null;
    }

    /**
     * @param string|array $key
     * @param $value
     * @param int|null $ttl
     * @param array $tags
     *
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     */
    public static function set($key, $value, int $ttl = null, array $tags = [])
    {
        $cache = self::getCacheService()->user();
        $item = $cache->getItem(self::key($key));
        $item->set($value);
        if ($ttl) {
            $item->expiresAfter($ttl);
        }
        if ($tags && $item instanceof CacheItem) {
            $item->tag($tags);
        }
        self::getCacheService()->user()->save($item);
    }

    /**
     * @param string|array $key
     * @param \Closure $closure
     * @param int|null $ttl
     * @param array $tags
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function getOrSet($key, \Closure $closure, int $ttl = null, array $tags = [])
    {
        $item = self::getCacheService()->user()->getItem(self::key($key));
        if ($item->isHit()) {
            return $item->get();
        }

        $result = $closure();
        $item->set($result);
        if ($ttl) {
            $item->expiresAfter($ttl);
        }
        if ($tags && $item instanceof CacheItem) {
            $item->tag($tags);
        }
        self::getCacheService()->user()->save($item);
        return $result;
    }

    /**
     * @param string|array $key
     *
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function invalidate($key)
    {
        self::getCacheService()->user()->deleteItem(self::key($key));
    }

    /**
     * @param array $tags
     *
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function invalidateTags(array $tags)
    {
        $cache = self::getCacheService()->user();
        if ($cache instanceof TagAwareAdapterInterface) {
            $cache->invalidateTags($tags);
        }
    }

    /**
     * @throws \RuntimeException
     */
    public static function clear()
    {
        self::getCacheService()->user()->clear();
    }

    /**
     * @param string|array $key
     * @param int|null $ttl
     * @param array $tags
     *
     * @return null|HtmlCache
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function html($key, int $ttl = null, array $tags = [])
    {
        if ($content = self::get($key)) {
            echo $content;
            return null;
        }

        ob_start();
        return new HtmlCache(self::getCacheService(), self::key($key), $ttl, $tags);
    }

    /**
     * Create PSR-6 valid key
     * @param string|array $key
     *
     * @return string
     */
    public static function key($key): string
    {
        if (!\is_string($key)) {
            $key = \json_encode($key);
        }
        return \preg_replace('/[\W]/', '_', $key);
    }

    /**
     * @param CacheService $service
     */
    public static function setCacheService(CacheService $service)
    {
        self::$cacheService = $service;
    }

    /**
     * @return CacheService
     * @throws \RuntimeException
     */
    protected static function getCacheService(): CacheService
    {
        if (!self::$cacheService) {
            throw new \RuntimeException('CacheService is not defined');
        }
        return self::$cacheService;
    }
}
