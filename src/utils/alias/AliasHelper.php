<?php

namespace WebComplete\core\utils\alias;

class AliasHelper
{

    protected static $aliasService;

    /**
     * @param string $alias
     * @param string $value
     *
     * @throws \Exception
     */
    public static function setAlias(string $alias, string $value)
    {
        self::getInstance()->setAlias($alias, $value);
    }

    /**
     * @param $alias
     * @param bool $throwException
     *
     * @return string|null
     * @throws \Exception
     */
    public static function get($alias, $throwException = true)
    {
        return self::getInstance()->get($alias, $throwException);
    }

    /**
     * @param AliasService $service
     */
    public static function setInstance(AliasService $service)
    {
        self::$aliasService = $service;
    }

    /**
     * @return AliasService
     * @throws \WebComplete\core\utils\alias\AliasException
     */
    protected static function getInstance(): AliasService
    {
        return self::$aliasService ?? new AliasService([]);
    }
}
