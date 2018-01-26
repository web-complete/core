<?php

namespace WebComplete\core\utils\cache;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;

class Cache
{
    /**
     * @var CacheService
     */
    protected static $cacheService;

    /**
     * @param string $key
     *
     * @return mixed|null
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function get(string $key)
    {
        $item = self::getCacheService()->user()->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }
        return null;
    }

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @param array $tags
     *
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     */
    public static function set(string $key, $value, int $ttl = null, array $tags = [])
    {
        $cache = self::getCacheService()->user();
        $item = $cache->getItem($key);
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
     * @param string $key
     * @param \Closure $closure
     * @param int|null $ttl
     * @param array $tags
     *
     * @return mixed
     * @throws \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function getOrSet(string $key, \Closure $closure, int $ttl = null, array $tags = [])
    {
        $item = self::getCacheService()->user()->getItem($key);
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
     * @param string $key
     *
     * @throws \RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function invalidate(string $key)
    {
        self::getCacheService()->user()->deleteItem($key);
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
     * @param string $key
     * @param int|null $ttl
     * @param array $tags
     *
     * @return null|HtmlCache
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function html(string $key, int $ttl = null, array $tags = [])
    {
        if ($content = self::get($key)) {
            echo $content;
            return null;
        }

        ob_start();
        return new HtmlCache(self::getCacheService(), $key, $ttl, $tags);
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
