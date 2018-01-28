<?php

namespace WebComplete\core\utils\cache;

class CacheRuntime
{
    protected static $cache = [];

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public static function get(string $key)
    {
        return self::$cache[$key] ?? null;
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value)
    {
        self::$cache[$key] = $value;
    }

    /**
     * @param string $key
     * @param \Closure $closure
     *
     * @return mixed
     */
    public static function getOrSet(string $key, \Closure $closure)
    {
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = $closure();
        }
        return self::$cache[$key];
    }

    /**
     * @param string $key
     */
    public static function invalidate(string $key)
    {
        unset(self::$cache[$key]);
    }

    /**
     */
    public static function clear()
    {
        self::$cache = [];
    }
}
