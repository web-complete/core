<?php

namespace WebComplete\core\utils\alias;

class AliasService
{
    /**
     * @var array
     */
    protected $aliases;

    /**
     * @param array|null $aliases
     *
     * @throws AliasException
     */
    public function __construct(array $aliases = null)
    {
        if ($aliases !== null) {
            foreach ($aliases as $alias => $value) {
                $this->setAlias($alias, $value);
            }
        }
    }

    /**
     * @param string $alias
     * @param string $value
     *
     * @throws AliasException
     */
    public function setAlias(string $alias, string $value)
    {
        if (0 !== \strpos($alias, '@')) {
            throw new AliasException('Alias should starts with @');
        }
        $this->aliases[$alias] = $value;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param string $alias
     * @param bool $throwException
     *
     * @return string|null
     * @throws AliasException
     */
    public function get(string $alias, $throwException = true)
    {
        if (0 !== \strpos($alias, '@')) {
            return $alias;
        }

        $pos = \strpos($alias, '/');
        /** @var string $root */
        $root = $pos === false ? $alias : \substr($alias, 0, $pos);

        if (isset($this->aliases[$root])) {
            return $pos === false ? $this->aliases[$root] : $this->aliases[$root] . \substr($alias, $pos);
        }

        if ($throwException) {
            throw new AliasException("Invalid alias: $alias");
        }

        return null;
    }
}
