<?php

namespace WebComplete\core\utils\cache;

class CacheRuntime
{
    protected static $cache = [];

    /**
     * @param string|array $key
     *
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::$cache[Cache::key($key)] ?? null;
    }

    /**
     * @param string|array $key
     * @param $value
     */
    public static function set($key, $value)
    {
        self::$cache[Cache::key($key)] = $value;
    }

    /**
     * @param string|array $key
     * @param \Closure $closure
     *
     * @return mixed
     */
    public static function getOrSet($key, \Closure $closure)
    {
        $key = Cache::key($key);
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = $closure();
        }
        return self::$cache[$key];
    }

    /**
     * @param string|array $key
     */
    public static function invalidate($key)
    {
        unset(self::$cache[Cache::key($key)]);
    }

    /**
     */
    public static function clear()
    {
        self::$cache = [];
    }
}
